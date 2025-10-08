pipeline {
    agent any

    environment {
        // --- Deployment Details ---
        ARTIFACT_DIR = 'build_artifact'
        TAR_NAME     = 'magento-clean.tar.gz'

        // --- Remote Server Details ---
        REMOTE_USER  = 'cm'
        REMOTE_IP    = '172.18.147.53' // Your target IP
        REMOTE_PATH  = '/var/www/html/magento2'

        // --- Jenkins Credential ID (Configure in Jenkins Credentials) ---
        // 🔑 CRITICAL: Replace 'magento-deployment-cred' with the actual ID
        // of your 'Username with password' credential in Jenkins.
        CREDENTIAL_ID = 'test@123' 

        // 🚨 Security Fix: The original hardcoded SSH_PASSWORD has been removed.
        // It is now securely retrieved using the CREDENTIAL_ID.
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
                echo "Building clean artifact (excluding vendor/, generated/, pub/static/)..."
                script {
                    def excludes = [
                        '.git/', 'var/', 'vendor/', 'generated/', 'pub/static/',
                        'pub/media/', 'node_modules/', 'dev/', 'phpserver/',
                        '.idea/', '*.log', 'setup/'
                    ]
                    def excludeParams = excludes.collect { "--exclude='${it}'" }.join(' ')

                    sh """
                        mkdir -p ${ARTIFACT_DIR}
                        # Use rsync to copy files, excluding large/generated directories
                        rsync -av --delete ${excludeParams} ./ ${ARTIFACT_DIR}/
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
                // Securely bind the credentials (Username & Password) to variables
                withCredentials([
                    usernamePassword(credentialsId: CREDENTIAL_ID, usernameVariable: 'REMOTE_UNAME', passwordVariable: 'REMOTE_PASS')
                ]) {
                    script {
                        echo "Starting secured upload and extraction on ${REMOTE_IP}..."

                        // 1. Define the remote connection map using the secure variables
                        def remote = [
                            name: 'magento_server',
                            host: REMOTE_IP,
                            user: REMOTE_UNAME, // Secured username
                            password: REMOTE_PASS, // Secured password
                            allowAnyHosts: true
                        ]

                        try {
                            timeout(time: 5, unit: 'MINUTES') {

                                // 2. Upload the tarball using sshPut
                                echo "Uploading ${TAR_NAME} to ${REMOTE_PATH}..."
                                sshPut remote: remote, from: TAR_NAME, into: REMOTE_PATH, failOnError: true

                                // 3. Execute the extraction command using sshCommand
                                def extractCommand = """
                                    set -e
                                    cd ${REMOTE_PATH}
                                    echo "Extracting artifact..."
                                    # Extract contents of the tarball (which contains the deployment files)
                                    tar xzf ${TAR_NAME}
                                    echo "Removing tarball..."
                                    rm ${TAR_NAME}
                                """
                                echo "Executing remote extraction commands..."
                                sshCommand remote: remote, command: extractCommand, failOnError: true
                            }
                        } catch (err) {
                            error "Deployment failed during upload/extract: ${err}"
                        }
                    }
                }
            }
        }

        stage('Magento Deployment Commands') {
            steps {
                // Securely bind the credentials for the CLI commands
                withCredentials([
                    usernamePassword(credentialsId: CREDENTIAL_ID, usernameVariable: 'REMOTE_UNAME', passwordVariable: 'REMOTE_PASS')
                ]) {
                    script {
                        def remote = [
                            name: 'magento_server',
                            host: REMOTE_IP,
                            user: REMOTE_UNAME,
                            password: REMOTE_PASS,
                            allowAnyHosts: true
                        ]

                        // The full set of Magento 2 CLI commands for deployment
                        def magentoCommands = """
                            set -e
                            cd ${REMOTE_PATH}

                            # 1. Enable Maintenance Mode to prevent users accessing the site during update
                            echo "Enabling Magento maintenance mode..."
                            php bin/magento maintenance:enable

                            # 2. Update dependencies (vendor/ was excluded from artifact)
                            echo "Running composer install..."
                            composer install --no-dev --optimize-autoloader

                            # 3. Clear cache, run database migrations, and compile code
                            echo "Running setup:upgrade and compiling code..."
                            # --keep-generated preserves generated code until compilation finishes
                            php bin/magento setup:upgrade --keep-generated
                            php bin/magento setup:di:compile

                            # 4. Deploy static content (Change 'en_US' to your required locale(s))
                            echo "Deploying static content..."
                            php bin/magento setup:static-content:deploy en_US -f

                            # 5. Clear cache and Disable Maintenance Mode
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
    }

    post {
        success {
            echo "✅ Deployment complete. Magento application is now updated on ${REMOTE_IP}:${REMOTE_PATH}"
        }
        failure {
            echo "❌ Deployment failed. Check logs for errors."
        }
        always {
            echo "Pipeline finished at: ${new Date()}"
        }
    }
}
