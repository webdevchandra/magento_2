pipeline {
    agent any

    environment {
        // --- Connection Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'cm'
        REMOTE_IP    = '172.18.147.53' // Your target IP
        REMOTE_PATH  = '/var/www/html/magento2'
        
        // 🚨 CRITICAL SECURITY RISK: Password hardcoded here
        SSH_PASSWORD = 'test@123' 
        SUDO_PASSWORD = 'test@123'
    }

    stages {
        stage('Prepare') {
            steps {
                echo "Cleaning old workspace..."
                sh "rm -rf ${ARTIFACT_DIR} ${TAR_NAME}"
            }
        }

        stage('Build Artifact') {
            steps {
                echo "Building clean artifact..."
                script {
                    def excludes = [
                        '.git/', 
                        'pub/media/', 'node_modules/', 'dev/', 'phpserver/', 
                        '.idea/', '*.log', 'setup/', 'vendor/','app/', 'lib','var/'
                    ]
                    def excludeParams = excludes.collect { "--exclude='${it}'" }.join(' ')

                    sh """
                        mkdir -p ${ARTIFACT_DIR}
                        rsync -av ${excludeParams} ./ ${ARTIFACT_DIR}/
                    """
                }
            }
        }

        stage('Archive Tarball') {
            steps {
                echo "Creating tar.gz archive..."
                sh "tar czf ${TAR_NAME} -C ${ARTIFACT_DIR} ."
                archiveArtifacts artifacts: "${TAR_NAME}", fingerprint: true
            }
        }

        // ---------------------------------------------------------------------
        // FIX: Using SUDO for extraction and CHOWN to fix permissions
        // ---------------------------------------------------------------------
        stage('Upload and Extract') {
            steps {
                script {
                    echo "Starting secured upload and extraction on ${REMOTE_IP}..."

                    // 1. Define the remote connection map
                    def remote = [
                        name: 'magento_server',
                        host: REMOTE_IP,
                        user: REMOTE_USER,
                        password: SSH_PASSWORD,
                        allowAnyHosts: true 
                    ]
                    
                    try {
                        timeout(time: 10, unit: 'MINUTES') { 
                            
                            // 2. Upload the tarball using sshPut
                            echo "Uploading ${TAR_NAME} to ${REMOTE_PATH}..."
                            sshPut remote: remote, from: TAR_NAME, into: REMOTE_PATH, failOnError: true

                            // 3. Execute the extraction, ownership fix, and composer install using sshCommand
                            def remoteCommand = """
                            set -e
                            cd ${REMOTE_PATH}
                        
                            echo "Extracting artifact with sudo..."
                            # Use sudo to allow tar to change file metadata (utime/mode) in the web root.
                            # We use 'sh -c' to ensure proper command grouping and execution via sudo.
                            # NOTE: This requires 'cm' user to have passwordless sudo or a system to handle the password.
                            # If password is required, you may need the 'expect' plugin.
                            sudo tar xzf ${TAR_NAME}
                            
                            echo "Setting ownership of all extracted files to ${REMOTE_USER}..."
                            # Set ownership back to the deployment user to allow composer/magento commands to run without sudo.
                            sudo chown -R ${REMOTE_USER}:${REMOTE_USER} .
                            
                            echo "Removing tarball..."
                            rm ${TAR_NAME}
                            
                            echo "Running composer install..."
                            #composer install --no-dev --prefer-dist --optimize-autoloader
                            """

                            echo "Executing remote extraction and setup commands..."
                            sshCommand remote: remote, command: remoteCommand, failOnError: true
                        }
                    } catch (err) {
                        error "Deployment failed: Check network connection, plugin installation, or credentials: ${err}"
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // NEW REMOTE STAGE: This executes all Magento CLI commands on the remote server
        // ---------------------------------------------------------------------
        stage('Magento Deployment Commands (Remote)') {
            steps {
                script {
                    def remote = [
                        name: 'magento_server',
                        host: REMOTE_IP,
                        user: REMOTE_USER,
                        password: SSH_PASSWORD,
                        allowAnyHosts: true 
                    ]

                    def magentoCommand = """
                    set -e
                    cd ${REMOTE_PATH}
                    
                    echo "Setting Magento file system permissions using sudo..."
                    # Since the previous stage's composer install or other environment factors may have created files 
                    # with restrictive permissions, we use 'sudo' here to guarantee the chmod operations succeed.
                    #sudo find var generated pub/static pub/media app/etc -type d -exec chmod u+w,g+w {} +
                    #sudo find var generated pub/static pub/media app/etc -type f -exec chmod u+w,g+w {} +
                    #sudo chmod u+x bin/magento
                    
                    # Re-apply ownership using sudo to ensure the deployment user ('cm') owns any files 
                    # created since the previous stage and has full control before running Magento commands.
                    sudo chown -R ${REMOTE_USER}:${REMOTE_USER} .

                    echo "Running Magento setup upgrade..."
                    #php bin/magento setup:upgrade --keep-generated
                    composer dump-autoload
                    echo "Compiling Magento (Dependency Injection)..."
                    php bin/magento setup:di:compile

                    echo "Deploying static content..."
                    php bin/magento setup:static-content:deploy en_US -f

                    echo "Flushing cache..."
                    php bin/magento cache:flush
                    
                    # FINAL OWNERSHIP FIX: In a production setup, you would typically use chown here 
                    # to set the ownership to the correct web server user (e.g., www-data).
                    # Example: sudo chown -R www-data:www-data ${REMOTE_PATH}
                    
                    echo "✅ Magento deployment done."
                    """
                    echo "Executing Magento deployment commands remotely..."
                    sshCommand remote: remote, command: magentoCommand, failOnError: true
                }
            }
        }
    }

    post {
        success {
            echo "Artifact uploaded and extracted successfully on ${REMOTE_IP}:${REMOTE_PATH}"
        }
        failure {
            echo "Deployment failed. Check logs for errors."
        }
        always {
            echo "Pipeline finished at: ${new Date()}"
        }
    }
}
