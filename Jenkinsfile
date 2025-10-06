// Jenkinsfile

pipeline {
    agent any // 1. Defines where the pipeline runs (on any available agent/node)
    
    stages {
        // 2. Stage to perform project setup
        stage('Setup') {
            steps {
                echo 'Starting Pipeline setup...'
                // Since you are on Ubuntu WSL, use 'sh' for shell commands
                sh 'echo "Running setup steps like installing dependencies (e.g., npm install)"'
            }
        }
        
        // 3. Stage to run tests
        stage('Test') {
            steps {
                echo 'Running tests...'
                // Placeholder for running your Robot Framework tests or unit tests
                sh 'robot your_tests/test_suite.robot || true' // '|| true' allows the build to continue if tests fail for now
            }
        }
        
        // 4. Stage to build an artifact (if applicable)
        stage('Build') {
            steps {
                echo 'Building application or packaging artifacts...'
                // Placeholder for a simple build command
                sh 'mkdir -p build && echo "App built at $(date)" > build/app.log'
            }
        }
        
        // 5. Stage for deployment (if the build is successful)
        stage('Deploy') {
            when {
                // Only run this stage if the previous stages succeeded
                expression { return currentBuild.result == 'SUCCESS' }
            }
            steps {
                echo 'Deploying to the environment...'
                // Placeholder for deployment command
                sh 'echo "Deployment to the server initiated..."'
            }
        }
    }
    
    // 6. Post-build actions (run regardless of success/failure)
    post {
        always {
            echo 'Pipeline finished.'
            // Clean up workspace after build
            // cleanWs() 
        }
        success {
            echo 'Build succeeded! Sending success notification.'
        }
        failure {
            echo 'Build failed! Review the logs.'
        }
    }
}
