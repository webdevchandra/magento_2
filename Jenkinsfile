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
                        timeout(time: 10, unit: 'MINUTES') { // INCREASED TIMEOUT to 10 minutes to accommodate composer install
                            
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
        sh '''#!/bin/bash
            set -e
            cd /var/www/html/magento2
            echo "Running composer install to fetch dependencies..."
            composer install --ignore-platform-reqs
            echo "Running dump-autoload..."
            composer dump-autoload
        
            echo "Running sudo permissions ..."
            echo "test@123" | sudo -S chown -R root:root /var/www/html/magento2/
        
            echo "Running chmod permissions..."
            echo "test@123" | sudo -S chmod -R 777 /var/www/html/magento2/generated/ /var/www/html/magento2/pub/ /var/www/html/magento2/var/cache/ /var/www/html/magento2/var/page_cache/
            echo "End permissions..."
             echo "setup upgrade..."
            echo "test@123" | sudo -S php bin/magento setup:upgrade
            echo "setup compile..."
            echo "test@123" | sudo -S php bin/magento setup:di:compile
            echo "setup static content..."
            echo "test@123" | sudo -S php bin/magento setup:static-content:deploy en_US -f
            echo "cache flush..."
            echo "test@123" | sudo -S php bin/magento cache:flush
             echo "commands done ..."
        '''
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
