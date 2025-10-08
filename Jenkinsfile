pipeline {
    agent any

    environment {
        // --- Connection Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'cm'
        REMOTE_IP    = '172.18.147.53' // The confirmed reachable IP
        REMOTE_PATH  = '/var/www/html/magento2'
        
        // --- Custom SSH Timeout (10 seconds) ---
        SSH_CONN_TIMEOUT = '10' 
        
        // 🚨 CRITICAL SECURITY RISK: Password hardcoded here and exposed in logs
        SSH_PASSWORD = 'test@123' 
    }

    stages {
        // ... (Prepare, Build Artifact, Archive Tarball stages remain the same) ...

        stage('Upload and Extract') {
            steps {
                script {
                    echo "Starting upload and extraction on ${env.REMOTE_IP}..."
                    sh 'command -v sshpass || { echo "ERROR: sshpass utility not found. Install it on the Jenkins agent."; exit 1; }'

                    try {
                        timeout(time: 5, unit: 'MINUTES') {
                            sh """
                            # Set local shell variables to guarantee correct interpolation
                            IP="${REMOTE_IP}"
                            USER="${REMOTE_USER}"
                            PASS="${SSH_PASSWORD}"
                            TIMEOUT="${SSH_CONN_TIMEOUT}"

                            # 1. Upload the tarball using scp
                            echo "--- Executing SCP Command to \$IP ---"
                            sshpass -p "\$PASS" scp \\
                                -o StrictHostKeyChecking=no \\
                                -o ConnectTimeout=\$TIMEOUT \\
                                -P 22 ${TAR_NAME} \\
                                \$USER@\$IP:${REMOTE_PATH}/
                            
                            # 2. Run remote extraction commands via SSH
                            echo "--- Executing SSH Command to \$IP for Extraction ---"
                            sshpass -p "\$PASS" ssh \\
                                -o StrictHostKeyChecking=no \\
                                -o ConnectTimeout=\$TIMEOUT \\
                                -P 22 \$USER@\$IP '
                                
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
