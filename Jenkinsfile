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
        
        // --- Stage 1: Setup & Code Sync ---
        stage('Setup and Install') {
            steps {
                echo 'Starting setup: installing dependencies and configuring environment...'
                
                // Assuming your requirements.txt is in the Git root (Jenkins WORKSPACE)
                
                // You can add logic here to copy the newly cloned code from 
                // ${WORKSPACE} to ${MAGENTO_ROOT} if needed, but often
                // Git is configured to push directly to the MAGENTO_ROOT.
            }
        }

        // --- Stage 2: Magento Deployment Tasks ---
        stage('Magento Deployment Tasks') {
            steps {
                echo "Running build and deployment tasks inside: ${MAGENTO_ROOT}"
                
                // 🔑 CRITICAL: Use 'dir' to change the working directory to the Magento root.
                dir("${MAGENTO_ROOT}") {
                    echo 'Cleaning caches and compiling static content...'
                    
                    // ➡️ ADD YOUR MAGENTO COMMANDS HERE (use 'sudo' if the Jenkins user needs it)
                    sh 'echo "Running: sudo bin/magento cache:clean"'
                    sh 'echo "Running: sudo bin/magento setup:upgrade"'
                    
                    sh 'echo "Magento commands placeholder executed."'
                }
            }
        }
        
        // --- Stage 3: Conditional Action (External Testing) ---
        stage('Conditional Action') {
            steps {
                script {
                     echo 'DEV/QA Environment detected. Executing conditional action...'
                        // These would typically be running automated tests.
                        sh 'echo "Dev/QA action executed "'
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
