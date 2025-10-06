// Define common variables for the pipeline (You can fill these in later)
def PLACE_ORDER_LOCATOR = "css:button.action.primary.checkout[data-role='review-save']"

pipeline {
    // Run the pipeline on any available agent that can execute shell commands (e.g., your Ubuntu server)
    agent any
    
    // Environment: Define variables (optional, but good for defining tool paths)
    environment {
        // Example: Ensure global Python/shell paths are available
        PATH = "/usr/local/bin:$PATH" 
    }
    
    stages {
        
        // --- Stage 1: Setup & Dependencies ---
        stage('Setup and Install') {
            steps {
                echo 'Starting setup: installing dependencies and configuring environment...'
                
                // ➡️ ADD YOUR COMMANDS HERE (e.g., sh 'pip install -r requirements.txt')
                sh 'echo "Dependency installation placeholder."' 
            }
        }

        // --- Stage 2: Build/Compile ---
        stage('Build/Package Artifacts') {
            steps {
                echo 'Building or packaging the application/artifacts...'
                
                // ➡️ ADD YOUR BUILD/COMPILATION COMMANDS HERE (e.g., sh 'npm run build' or sh 'dotnet build')
                sh 'echo "Build step placeholder."'
            }
        }
        
        // --- Stage 3: Conditional Action (Place Order Check) ---
        stage('Conditional Action') {
            steps {
                script {
                    // Check the current environment URL based on the repository URL (as a proxy for the actual environment)
                    // NOTE: In a real pipeline, you often use an Environment Variable or a parameter for this check.
                    
                    // Fetch the Git URL that Jenkins used for cloning
                    def gitUrl = sh(returnStdout: true, script: 'echo $GIT_URL').trim().toLowerCase()
                    def isDevEnv = gitUrl.contains("stage.") || gitUrl.contains("qa.")
                    
                    if (isDevEnv) {
                        echo 'DEV/QA Environment detected. Executing conditional action (e.g., Place Order click)...'
                        
                        // ➡️ ADD YOUR DEV/QA-SPECIFIC COMMANDS HERE 
                        sh 'echo "Dev/QA action executed successfully. (e.g., Clicking ${PLACE_ORDER_LOCATOR})"'
                        
                    } else {
                        echo 'PROD Environment detected. Skipping conditional action.'
                        // ➡️ ADD YOUR PROD-SPECIFIC LOGGING OR ACTIONS HERE
                    }
                }
            }
        }
    }
    
    // --- Post-Build Actions ---
    post {
        always {
            echo 'Pipeline finished. Cleaning up workspace.'
            // ➡️ ADD YOUR CLEANUP COMMANDS HERE (e.g., cleanWs() to delete files)
            // cleanWs() 
        }
        success {
            echo '✅ Build and pipeline succeeded.'
        }
        failure {
            echo '❌ Pipeline failed.'
        }
    }
}
