pipeline {
    agent any

    // Ensure you update the REMOTE_IP if the hostname resolution is failing
    // or if the IP changes frequently (common with WSL).
    environment {
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'ubuntu'
        REMOTE_IP    = '172.18.147.53' // <-- Use your IP if hostname fails (e.g., '172.24.72.193')
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Change if your web server uses a different user
        SSH_CREDENTIAL_ID = 'wsl-cm-ssh-key' // <-- MUST match your Jenkins Credential ID
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
                        '.git/', 'var/', 'vendor/', 'generated/', 
                        'pub/static/', 'pub/media/', 'node_modules/', 
                        'dev/', 'phpserver/', '.idea/', '*.log', 'setup/'
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
                echo "Creating tar.gz archive of cleaned Magento files..."
                sh "tar czf ${TAR_NAME} -C ${ARTIFACT_DIR} ."
                archiveArtifacts artifacts: "${TAR_NAME}", fingerprint: true
            }
        }

        stage('Upload to Remote Server') {
            steps {
                script {
                    echo "Uploading tarball to remote server securely..."

                    // Use withCredentials to securely expose the private key path
                    withCredentials([sshUserPrivateKey(credentialsId: env.SSH_CREDENTIAL_ID, keyFileVariable: 'SSH_KEY_FILE')]) {
                        timeout(time: 20, unit: 'MINUTES') {
                            // Use -i flag for the private key and StrictHostKeyChecking=no for the first connection
                            sh "scp -i ${SSH_KEY_FILE} -o StrictHostKeyChecking=no ${TAR_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                        }
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                script {
                    echo "Running deployment on server..."

                    // Use withCredentials again for the SSH command
                    withCredentials([sshUserPrivateKey(credentialsId: env.SSH_CREDENTIAL_ID, keyFileVariable: 'SSH_KEY_FILE')]) {
                        try {
                            timeout(time: 45, unit: 'MINUTES') {
                                // Use -i flag and StrictHostKeyChecking=no for non-interactive SSH
                                sh """
                                ssh -i ${SSH_KEY_FILE} -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_IP} '
                                    set -e
                                    cd ${REMOTE_PATH} &&
                                    echo "Extracting artifact..." &&
                                    tar xzf ${TAR_NAME} &&
                                    rm ${TAR_NAME} &&

                                    echo "Fixing permissions..." &&
                                    sudo chown -R ${WEB_USER}:${WEB_USER} ${REMOTE_PATH} &&

                                    echo "Running Composer..." &&
                                    sudo -u ${WEB_USER} composer install --no-dev --no-interaction &&

                                    echo "Magento setup upgrade..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:upgrade &&

                                    echo "Compiling DI..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:di:compile &&

                                    echo "Deploying static content..." &&
                                    sudo -u ${WEB_USER} php bin/magento setup:static-content:deploy -f &&pipeline {
    agent any

    // Ensure you update the REMOTE_IP if the hostname resolution is failing
    // or if the IP changes frequently (common with WSL).
    environment {
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'ubuntu'
        REMOTE_IP    = '172.18.147.53' // <-- Use your IP if hostname fails (e.g., '172.24.72.193')
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Change if your web server uses a different user
        SSH_CREDENTIAL_ID = 'wsl-cm-ssh-key' // <-- MUST match your Jenkins Credential ID
    }

    stages { // <--- Start of stages block

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
                        '.git/', 'var/', 'vendor/', 'generated/', 
                        'pub/static/', 'pub/media/', 'node_modules/', 
                        'dev/', 'phpserver/', '.idea/', '*.log', 'setup/'
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
                echo "Creating tar.gz archive of cleaned Magento files..."
                sh "tar czf ${TAR_NAME} -C ${ARTIFACT_DIR} ."
                archiveArtifacts artifacts: "${TAR_NAME}", fingerprint: true
            }
        }

        stage('Upload to Remote Server') {
            steps {
                script {
                    echo "Uploading tarball to remote server securely..."

                    // Use withCredentials to securely expose the private key path
                    withCredentials([sshUserPrivateKey(credentialsId: env.SSH_CREDENTIAL_ID, keyFileVariable: 'SSH_KEY_FILE')]) {
                        timeout(time: 20, unit: 'MINUTES') {
                            // Use -i flag for the private key and StrictHostKeyChecking=no for the first connection
                            sh "scp -i ${SSH_KEY_FILE} -o StrictHostKeyChecking=no ${TAR_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                        }
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                script {
                    echo "Running deployment on server..."

                    // Use withCredentials again for the SSH command
                    withCredentials([sshUserPrivateKey(credentialsId: env.SSH_CREDENTIAL_ID, keyFileVariable: 'SSH_KEY_FILE')]) {
                        try {
                            timeout(time: 45, unit: 'MINUTES') {
                                // Use -i flag and StrictHostKeyChecking=no for non-interactive SSH
                                sh """
                                ssh -i ${SSH_KEY_FILE} -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_IP} '
                                    set -e
                                    cd ${REMOTE_PATH} &&
                                    echo "Extracting artifact..." &&
                                    tar xzf ${TAR_NAME} &&
                                    rm ${TAR_NAME} &&

                                    echo "Fixing permissions..." &&
                                    sudo chown -R ${WEB_USER}:${WEB_USER} ${REMOTE_PATH} &&

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
                        } catch (err) {
                            error "Deployment failed: ${err}"
                        }
                    }
                }
            }
        }
    } // <--- This is the MISSING CLOSING BRACE that fixes the compilation error

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

                                    echo "Flushing cache..." &&
                                    sudo -u ${WEB_USER} php bin/magento cache:flush
                                '
                                """
                            }
                        } catch (err) {
                            error "Deployment failed: ${err}"
                        }
                    }
                }
            }
        }
    }
