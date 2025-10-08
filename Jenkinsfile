pipeline {
    agent any

    environment {
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'ubuntu'
        REMOTE_IP    = '172.18.147.53'
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Web server user on the remote host (Ubuntu/Debian default)
        
        // ID of the SSH credential configured in Jenkins
        SSH_CREDENTIAL_ID = 'wsl-cm-ssh-key' 
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
                echo "Creating tar.gz archive of cleaned Magento files..."
                sh "tar czf ${TAR_NAME} -C ${ARTIFACT_DIR} ."
                archiveArtifacts artifacts: "${TAR_NAME}", fingerprint: true
            }
        }

        stage('Upload to Remote Server') {
            steps {
                echo "Uploading tarball to remote server securely..."
                // Use sshagent to load the key for the SSH/SCP process
                sshagent([env.SSH_CREDENTIAL_ID]) {
                    timeout(time: 20, unit: 'MINUTES') {
                        // -o StrictHostKeyChecking=no bypasses the "Host key verification failed" error on first run
                        sh "scp -o StrictHostKeyChecking=no ${TAR_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                echo "Running deployment on server..."
                // Use sshagent again for the SSH command
                sshagent([env.SSH_CREDENTIAL_ID]) {
                    // Use a script block to allow Groovy error handling (try/catch)
                    script {
                        try {
                            timeout(time: 45, unit: 'MINUTES') {
                                sh """
                                // -o StrictHostKeyChecking=no bypasses the host key verification
                                ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_IP} '
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
    }

    post {
        success {
            echo "Deployment to ${REMOTE_IP}:${REMOTE_PATH} successful! 🎉"
        }
        failure {
            echo "Deployment failed. Check logs for errors. 😔"
        }
        always {
            echo "Pipeline finished at: ${new Date()}"
        }
    }
}
