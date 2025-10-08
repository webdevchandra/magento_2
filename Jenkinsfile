pipeline {
    agent any

    environment {
        // --- Connection Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'cm'
        REMOTE_IP    = '172.18.147.53' // Your target IP
        REMOTE_PATH  = '/var/www/html/magento2'
        
        // --- Custom SSH Timeout (10 seconds) ---
        SSH_CONN_TIMEOUT = '10' 
        
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
        // Simplified Deployment Stage
        // ---------------------------------------------------------------------
        stage('Upload and Extract') {
            steps {
                script {
                    echo "Starting upload and extraction on ${env.REMOTE_IP}..."
                    
                    // Check for required external tool before running
                    sh 'command -v sshpass || { echo "ERROR: sshpass utility not found. Install it on the Jenkins agent."; exit 1; }'

                    try {
                        timeout(time: 5, unit: 'MINUTES') {
                            sh """
                            # 1. Upload the tarball using scp
                            echo "--- Executing SCP Command ---"
                            sshpass -p "${SSH_PASSWORD}" scp \\
                                -o StrictHostKeyChecking=no \\
                                -o ConnectTimeout=${SSH_CONN_TIMEOUT} \\
                                -P 22 ${TAR_NAME} \\
                                ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/
                            
                            # 2. Run remote extraction commands via SSH
                            echo "--- Executing SSH Command for Extraction ---"
                            sshpass -p "${SSH_PASSWORD}" ssh \\
                                -o StrictHostKeyChecking=no \\
                                -o ConnectTimeout=${SSH_CONN_TIMEOUT} \\
                                -P 22 ${REMOTE_USER}@${REMOTE_IP} '
                                
                                set -e
                                cd ${REMOTE_PATH} &&
                                
                                echo "Extracting artifact..." &&
                                tar xzf ${TAR_NAME} &&
                                
                                echo "Removing tarball..." &&
                                rm ${TAR_NAME}
                            '
                            """
                        }
                    } catch (err) {
                        error "Deployment failed. Check network connection and firewall: ${err}"
                    }
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
