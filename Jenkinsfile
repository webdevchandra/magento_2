pipeline {
    agent any

    environment {
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'root'
        REMOTE_IP    = '172.18.147.53'
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Change if your web server uses a different user
        // NEW: Credential ID for SSH authentication
        SSH_CREDENTIAL_ID = 'test@123' // <<== *** REPLACE THIS ID ***
    }

    stages {

        stage('Clean Workspace') {
            steps {
                echo "Cleaning old workspace..."
                sh "rm -rf ${ARTIFACT_DIR} ${TAR_NAME}"
            }
        }

        stage('Build Clean Magento Artifact') {
            steps {
                echo "Building clean Magento 2 artifact..."

                script {
                    def excludes = [
                        '.git/',
                        'var/',
                        'vendor/',
                        'generated/',
                        'pub/static/',
                        'pub/media/',
                        'node_modules/',
                        'dev/',
                        'phpserver/',
                        '.idea/',
                        '*.log',
                        'setup/'
                    ]

                    // The quotes around the exclude variable are fixed here
                    def excludeParams = excludes.collect { "--exclude='${it}'" }.join(' ')

                    sh """
                        mkdir -p ${ARTIFACT_DIR}
                        # Using rsync to copy project files, excluding unnecessary directories
                        rsync -av ${excludeParams} ./ ${ARTIFACT_DIR}/
                    """
                }
            }
        }

        stage('Archive Tarball') {
            steps {
                echo "Creating tar.gz archive of cleaned Magento files..."
                // Create tarball from the contents of the artifact directory
                sh "tar czf ${TAR_NAME} -C ${ARTIFACT_DIR} ."
                archiveArtifacts artifacts: "${TAR_NAME}", fingerprint: true
            }
        }

        // ---------------------------------------------------------------------
        // FIX APPLIED: Wrapping scp/ssh with sshagent for secure authentication
        // ---------------------------------------------------------------------
        stage('Upload to Remote Server') {
            steps {
                script {
                    echo "Uploading tarball to remote server..."

                    timeout(time: 20, unit: 'MINUTES') {
                        // FIX: Use sshagent to load the SSH key before SCP
                        sshagent([SSH_CREDENTIAL_ID]) {
                            // -o StrictHostKeyChecking=no is often needed on first connection
                            sh "scp -o StrictHostKeyChecking=no ${TAR_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                        }
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                script {
                    echo "Running deployment on server..."

                    try {
                        timeout(time: 45, unit: 'MINUTES') {
                            // FIX: Use sshagent to load the SSH key before the main deployment SSH command
                            sshagent([SSH_CREDENTIAL_ID]) {
                                sh """
                                # -o StrictHostKeyChecking=no is often needed on first connection
                                ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_IP} '
                                    set -e
                                    cd ${REMOTE_PATH} &&
                                    
                                    echo "Extracting artifact..." &&
                                    # Overwrite existing files in the current directory
                                    tar xzf ${TAR_NAME} &&
                                    rm ${TAR_NAME} &&

                                    echo "Fixing permissions..." &&
                                    # Ensure the web user owns all files for the next commands
                                    sudo chown -R ${WEB_USER}:${WEB_USER} . &&
                                    
                                    echo "Running Composer..." &&
                                    sudo -u ${WEB_USER} composer install --no-dev --no-interaction &&

                                    echo "Magento setup upgrade..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:upgrade &&

                                    echo "Compiling DI..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:di:compile &&

                                    echo "Deploying static content..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:static-content:deploy -f &&

                                    echo "Flushing cache..." &&
                                    sudo -u ${WEB_USER} php bin/magento cache:flush
                                '
                                """
                            }
                        }
                    } catch (err) {
                        error "Deployment failed: ${err}"
                    }
                }
            }
        }
    }

    post {
        success {
            echo "Magento 2 deployed successfully to ${REMOTE_IP}:${REMOTE_PATH}"
        }
        failure {
            echo "Deployment failed. Check logs for errors."
        }
        always {
            echo "Pipeline finished at: ${new Date()}"
        }
    }
}
