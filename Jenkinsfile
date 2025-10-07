pipeline {
    agent any

    environment {
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'ubuntu'
        REMOTE_IP    = '60.243.237.61'
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Change if your web server uses a different user
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
                        '*.log'
                    ]

                    // Build --exclude parameters for rsync
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
                    echo "Uploading tarball to remote server..."

                    timeout(time: 2, unit: 'MINUTES') {
                        sh "scp ${TAR_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                script {
                    echo "Running deployment on server..."

                    try {
                        timeout(time: 15, unit: 'MINUTES') {
                            sh """
                            ssh ${REMOTE_USER}@${REMOTE_IP} '
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
