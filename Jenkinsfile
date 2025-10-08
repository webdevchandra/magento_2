pipeline {
    agent any

    environment {
        // --- Connection Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'cm'
        REMOTE_IP    = '172.18.147.53'
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'cm' 
        
        // 🚨 CRITICAL SECURITY RISK: Password hardcoded here and exposed in logs
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

        stage('Upload and Deploy') {
            steps {
                script {
                    echo "Starting insecure upload and deployment..."
                    
                    // Check for required external tool before running
                    sh 'command -v sshpass || { echo "ERROR: sshpass utility not found. Install it on the Jenkins agent."; exit 1; }'

                    try {
                        timeout(time: 90, unit: 'MINUTES') {
                            sh """
                           # 1. Upload the tarball using scp
                            echo "Uploading tarball to ${env.REMOTE_IP}..."
                            sshpass -p "${SSH_PASSWORD}" scp -o StrictHostKeyChecking=no -P 22 ${TAR_NAME} \
                                ${env.REMOTE_USER}@${env.REMOTE_IP}:${REMOTE_PATH}/
                            
                            # 2. Run deployment commands via SSH
                            echo "Running remote deployment commands..."
                            sshpass -p "${SSH_PASSWORD}" ssh -o StrictHostKeyChecking=no -P 22 ${env.REMOTE_USER}@${env.REMOTE_IP} '
                                set -e
                                cd ${REMOTE_PATH} &&
                                
                                echo "Extracting artifact..." &&
                                tar xzf ${TAR_NAME} &&
                                rm ${TAR_NAME} &&

                                echo "Fixing permissions..." &&
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
