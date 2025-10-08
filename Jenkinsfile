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
                        '.git/', 'var/', 'vendor/', 'generated/', 'pub/static/', 
                        'pub/media/', 'node_modules/', 'dev/', 'phpserver/', 
                        '.idea/', '*.log', 'setup/'
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
        // FIX: Using SSH Pipeline Steps
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
                        allowAnyHosts: true // Equivalent to -o StrictHostKeyChecking=no
                    ]
                    
                    try {
                        timeout(time: 5, unit: 'MINUTES') {
                            
                            // 2. Upload the tarball using sshPut
                            echo "Uploading ${TAR_NAME} to ${REMOTE_PATH}..."
                            sshPut remote: remote, from: TAR_NAME, into: REMOTE_PATH, failOnError: true

                            // 3. Execute the extraction command using sshCommand
                            def remoteCommand = """
                                set -e
                                cd ${REMOTE_PATH}
                                echo "Extracting artifact..."
                                tar xzf ${TAR_NAME}
                                echo "Removing tarball..."
                                rm ${TAR_NAME}

                                # Run composer install to fetch dependencies (ADDED)
                                echo "Running composer install to fetch dependencies..."
                                composer install --no-dev --optimize-autoloader
                            """
                            echo "Executing remote extraction commands..."
                            // Use sshCommand to run the script remotely
                            sshCommand remote: remote, command: remoteCommand, failOnError: true
                        }
                    } catch (err) {
                        // The error message will now be cleaner if the connection fails
                        error "Deployment failed: Check network connection, plugin installation, or credentials: ${err}"
                    }
                }
            }
        }

        // Re-adding the Magento deployment commands needed for a complete pipeline
        stage('Magento Deployment Commands') {
            steps {
                script {
                    def remote = [
                        name: 'magento_server',
                        host: REMOTE_IP,
                        user: REMOTE_USER,
                        password: SSH_PASSWORD,
                        allowAnyHosts: true
                    ]

                    // The full set of Magento 2 CLI commands for deployment
                    def magentoCommands = """
                        set -e
                        cd ${REMOTE_PATH}

                        # 1. Enable Maintenance Mode to prevent users accessing the site during update
                        echo "Enabling Magento maintenance mode..."
                        php bin/magento maintenance:enable

                        # Composer install is skipped here as it ran in the previous stage.

                        # 2. Clear cache, run database migrations, and compile code
                        echo "Running setup:upgrade and compiling code..."
                        php bin/magento setup:upgrade --keep-generated
                        php bin/magento setup:di:compile

                        # 3. Deploy static content (Change 'en_US' to your required locale(s))
                        echo "Deploying static content..."
                        php bin/magento setup:static-content:deploy en_US -f

                        # 4. Clear cache and Disable Maintenance Mode
                        echo "Flushing cache and disabling maintenance mode..."
                        php bin/magento cache:flush
                        php bin/magento maintenance:disable
                    """
                    
                    echo "Executing remote Magento CLI commands..."
                    // Run all commands in a single ssh session for efficiency
                    sshCommand remote: remote, command: magentoCommands, failOnError: true
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
