pipeline {
    agent any
    environment {
        COMPOSE_FILE='./docker-compose-jenkins.yaml'
        PROJECT_NAME='TemplateProject'
        AWS_ACCOUNT_ID='079052685025'
        AWS_REGION='eu-south-2'
    }
    parameters {
        choice(name: "ENVIRONMENT", choices: ["", "production", "staging"], description: "Environment")
    }
    stages {
        stage('Build') {
            steps {
                echo 'Building..'
                withCredentials([usernamePassword(credentialsId: 'gitlab_registry', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {
                    sh 'docker login registry-gitlab.mo2o.com -u $USERNAME -p $PASSWORD'
                }
                sh 'docker compose build app --no-cache'
                sh 'docker compose up -d'
            }
        }
        stage('Configuration') {
            steps {
                echo "Waiting until app is ready.."
                sh 'docker compose run wait4x tcp app:9000 -t 60s -i 1s'
                sh 'docker compose exec app bin/console cache:clear --env=dev'
                sh 'docker compose exec app bin/console lexik:jwt:generate-keypair --env=test'
                sh 'docker compose exec app bin/console app:user:create_admin --env=test'
            }
        }
        stage('Security') {
            steps {
                sh 'docker compose exec app composer audit'
            }
        }
        stage('Test') {
            steps {
                echo 'Testing..'
                sh 'docker compose exec app bin/phpunit --colors --no-interaction --coverage-clover=sonar-reports/coverage-phpunit.xml'
                sh 'docker compose exec app vendor/bin/behat -f progress'
            }
        }
        stage('Analysis') {
            steps {
                sh 'docker compose exec app vendor/bin/phpstan --no-progress analyse -c phpstan.neon --error-format=json > sonar-reports/phpstan-report.json'
                sh 'docker compose exec app vendor/bin/phpcs -n'
                sh 'docker compose exec app vendor/bin/rector --dry-run --no-progress-bar '
                sh 'sed -i.back -e "s+/var/www/html/++" sonar-reports/coverage-phpunit.xml'
                sh 'sed -i.back -e "s+/var/www/html/++" sonar-reports/coverage-behat.xml'
                script {
                    // requires SonarQube Scanner 4.8+
                    scannerHome = tool 'SonarQube Scanner 4.8'
                }
                withSonarQubeEnv(installationName: 'SonarQube', credentialsId: 'jenkins-sonar') {
                    sh "${scannerHome}/bin/sonar-scanner"
                }
            }
        }
        stage('Deploy') {
            when {
                expression { return params.ENVIRONMENT != "" }
            }
            steps {
                echo "Deploying to $params.ENVIRONMENT ..."
                sh "docker image tag backend-template-app 079052685025.dkr.ecr.eu-south-2.amazonaws.com/backend-backend-template:$params.ENVIRONMENT-${env.BUILD_NUMBER}"
                script {
                    docker.withRegistry("https://079052685025.dkr.ecr.eu-south-2.amazonaws.com", "ecr:eu-south-2:jenkins_aws") {
                      docker.image("079052685025.dkr.ecr.eu-south-2.amazonaws.com/backend-backend-template:$params.ENVIRONMENT-${env.BUILD_NUMBER}").push()
                    }
                }
                withAWS(credentials: 'jenkins_aws') {
                   sh 'aws eks update-kubeconfig --region eu-south-2 --name TemplateProject'
                   sh "kubectl set image deployment/backend-svc backend=079052685025.dkr.ecr.eu-south-2.amazonaws.com/backend-backend-template:$params.ENVIRONMENT-${env.BUILD_NUMBER}"
                   sh "kubectl rollout status deployment/backend-svc  --watch --timeout=60s"
                }
            }
        }
    }
    post {
        success {
            slackSend(
                channel: "#jenkins",
                color: "good",
                message: "Successful Pipeline: ${currentBuild.fullDisplayName} :beers:"
            )
        }
        failure {
            sh "docker compose logs app"
//             slackSend(
//                 channel: "#jenkins",
//                 color: "danger",
//                 message: "Failed Pipeline: ${currentBuild.fullDisplayName} :warning:",
//             )
        }
        cleanup {
            echo "Clean up.."
            sh 'docker compose down --remove-orphans -v --rmi all'
            cleanWs()
        }
    }
}