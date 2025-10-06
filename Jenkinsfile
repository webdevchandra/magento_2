// Jenkinsfile

// Define the absolute path to your Magento installation
def MAGENTO_ROOT = '/var/www/html/magento2' 
pipeline {
    // 1. AGENT: Specifies that the job can run on any available Jenkins agent
    // that has access to your server's filesystem and shell commands (sh).
    agent any 

    // 2. ENVIRONMENT: Defines variables and paths for the build.
    environment {
        // Ensure standard system paths are available
        PATH = "/usr/local/bin:$PATH" 
        // Define a variable for the Git URL that can be used later
        GIT_URL = 'https://github.com/webdevchandra/magento_2.git' // UPDATE with your actual URL
    }
    
    stages {
        
        stage('Setup and Install') {
            steps {
                echo 'Starting setup: installing dependencies...'
                // These commands run in the Jenkins WORKSPACE (where the Git clone happened)
                sh 'pip install -r requirements.txt' 
            }
        }

        // --- Stage 2: Magento Deployment Tasks (Code Sync Added) ---
        stage('Magento Deployment Tasks') {
            steps {
                echo "Synchronizing code and running deployment tasks in: ${MAGENTO_ROOT}"
                
                // 🔑 CRITICAL: Use 'dir' to change the working directory to the Magento root.
                dir("${MAGENTO_ROOT}") {
                    
                    // 1. ADDED STEP: Copy files from the Jenkins workspace into the Magento root.
                    //    We use rsync for an efficient, reliable sync. The 'a' flag preserves permissions.
                    //    The trailing slash on ${WORKSPACE}/ is important to copy CONTENTS, not the folder itself.
                    sh "sudo rsync -av --exclude 'vendor' --exclude 'node_modules' ${WORKSPACE}/ ."
                    echo 'Code synchronized from Jenkins Workspace to Magento root.'

                    // 2. Run Magento commands on the newly synchronized code.
                    echo 'Cleaning caches and compiling static content...'
                    sh 'sudo bin/magento cache:clean'
                    sh 'sudo bin/magento setup:upgrade'
                    
                    sh 'echo "Magento commands executed."'
                }
            }
        }
    }
    
    // --- Post-Build Actions ---
    post {
        always {
            echo 'Pipeline finished. Cleaning up workspace.'
            // Clean up the temporary directory Jenkins uses for cloning the repo
            cleanWs() 
        }
        success {
            echo '✅ Deployment and pipeline succeeded.'
        }
        failure {
            echo '❌ Pipeline failed! Review the logs for errors in dependency installation or Magento commands.'
        }
    }
}
