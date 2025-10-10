pipeline {
    agent any

    environment {
        // --- Connection Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'
        REMOTE_USER  = 'root' // Use root for all remote operations
        REMOTE_IP    = '172.18.147.53'
        REMOTE_PATH  = '/var/www/html/magento2'

        // ⚠️ WARNING: Insecure hardcoded credentials
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

        stage('Upload and Extract') {
            steps {
                script {
                    echo "Starting secured upload and extraction on ${REMOTE_IP}..."

                    def remote = [
                        name: 'magento_server',
                        host: REMOTE_IP,
                        user: REMOTE_USER,     // root
                        password: SSH_PASSWORD,
                        allowAnyHosts: true
                    ]

                    try {
                        timeout(time: 10, unit: 'MINUTES') {
                            echo "Uploading ${TAR_NAME} to ${REMOTE_PATH}..."
                            sshPut remote: remote, from: TAR_NAME, into: REMOTE_PATH, failOnError: true

                            def remoteCommand = """
                                set -e
                                cd ${REMOTE_PATH}

                                echo "Extracting artifact..."
                                tar xzf ${TAR_NAME}
                                echo "Removing tarball..."
                                rm ${TAR_NAME}

                                echo "Installing dependencies via Composer..."
                                composer install --ignore-platform-reqs
                                composer dump-autoload

                                echo "Setting file permissions..."
                                chown -R cm:cm ${REMOTE_PATH}
                                chmod -R 777 ${REMOTE_PATH}/generated/ ${REMOTE_PATH}/pub/ ${REMOTE_PATH}/var/cache/ ${REMOTE_PATH}/var/page_cache/
                            """

                            echo "Executing remote commands as root..."
                            sshCommand remote: remote, command: remoteCommand, failOnError: true
                        }
                    } catch (err) {
                        error "Deployment failed: Check network connection, SSH access, or credentials: ${err}"
                    }
                }
            }
        }

        stage('Magento Deployment Commands') {
            steps {
                sh '''#!/bin/bash
                    set -e
                    cd /var/www/html/magento2
                    composer install --ignore-platform-reqs
                    php bin/magento maintenance:enable
                    php bin/magento setup:upgrade
                    php bin/magento setup:di:compile
                    php bin/magento setup:static-content:deploy en_US -f
                    php bin/magento cache:flush
                '''
            }
        }
    }

    post {
        success {
            echo "✅ Artifact uploaded and extracted successfully on ${REMOTE_IP}:${REMOTE_PATH}"
        }
        failure {
            echo "❌ Deployment failed. Check logs for errors."
        }
        always {
            echo "📅 Pipeline finished at: ${new Date()}"
        }
    }
}
