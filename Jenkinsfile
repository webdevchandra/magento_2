pipeline {
    agent any

    environment {
        ARTIFACT_DIR = 'build_artifact'
        ZIP_NAME     = 'magento-clean.zip'
        REMOTE_USER  = 'ubuntu'
        REMOTE_IP    = '61.242.231.6'
        REMOTE_PATH  = '/var/www/html/magento2'
        WEB_USER     = 'www-data' // Change if your web server uses a different user
    }

    stages {

        stage('Clean Workspace') {
            steps {
                echo "🧹 Cleaning old workspace..."
                sh "rm -rf ${ARTIFACT_DIR} ${ZIP_NAME}"
            }
        }

        stage('Build Clean Magento Artifact') {
            steps {
                echo "📦 Building clean Magento 2 artifact..."

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

                    def excludeParams = excludes.collect { "--exclude='${it}'" }.join(' ')

                    sh """
                        mkdir -p ${ARTIFACT_DIR}
                        rsync -av ${excludeParams} ./ ${ARTIFACT_DIR}/
                    """
                }
            }
        }

        stage('Archive Zip') {
            steps {
                echo "🗜️ Zipping the cleaned Magento 2 files..."
                sh "cd ${ARTIFACT_DIR} && zip -r ../${ZIP_NAME} ."
                archiveArtifacts artifacts: "${ZIP_NAME}", fingerprint: true
            }
        }

        stage('Upload to Remote Server') {
            steps {
                script {
                    echo "🚀 Uploading zip to remote server..."

                    timeout(time: 2, unit: 'MINUTES') {
                        sh "scp ${ZIP_NAME} ${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/"
                    }
                }
            }
        }

        stage('Deploy on Server') {
            steps {
                script {
                    echo "🔧 Running deployment on server..."

                    try {
                        timeout(time: 15, unit: 'MINUTES') {
                            sh """
                            ssh ${REMOTE_USER}@${REMOTE_IP} '
                                set -e
                                cd ${REMOTE_PATH} &&
                                echo "📦 Unzipping artifact..." &&
                                unzip -o ${ZIP_NAME} &&
                                rm ${ZIP_NAME} &&
                                
                                echo "🔐 Fixing permissions..." &&
                                sudo chown -R ${WEB_USER}:${WEB_USER} ${REMOTE_PATH} &&

                                echo "🎼 Running Composer..." &&
                                sudo -u ${WEB_USER} composer install --no-dev --no-interaction &&

                                echo "⚙️  Magento setup upgrade..." &&
                                sudo -u ${WEB_USER} php bin/magento setup:upgrade &&

                                echo "🧠 Compiling DI..." &&
                                sudo -u ${WEB_USER} php bin/magento setup:di:compile &&

                                echo "🎨 Deploying static content..." &&
                                sudo -u ${WEB_USER} php bin/magento setup:static-content:deploy -f &&

                                echo "🧹 Flushing cache..." &&
                                sudo -u ${WEB_USER} php bin/magento cache:flush
                            '
