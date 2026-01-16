pipeline {
  agent {
    docker {
      image 'docker:26-dind'
      args '-u root -v /var/run/docker.sock:/var/run/docker.sock'
    }
  }

  environment {
    IMAGE_PREFIX = 'inventory'
    DOCKER_CONFIG = "${WORKSPACE}/.docker"
  }

  stages {

    stage('Clone Repository') {
      steps {
        git branch: 'main',
            url: 'https://github.com/mohit838/inventory-management-with-laravel.git'
      }
    }

    stage('Resolve Git Commit') {
      steps {
        sh 'git config --global --add safe.directory "$WORKSPACE"'
        script {
          env.GIT_COMMIT_FULL = sh(
            script: 'git rev-parse HEAD',
            returnStdout: true
          ).trim()
          env.GIT_COMMIT_SHORT = sh(
            script: 'git rev-parse --short=7 HEAD',
            returnStdout: true
          ).trim()
        }
      }
    }

    stage('Prepare Docker') {
      steps {
        sh '''
          mkdir -p "$DOCKER_CONFIG"
          docker version
        '''
      }
    }

    stage('Load Environment File') {
      steps {
        withCredentials([
          file(credentialsId: 'laravel-inv', variable: 'ENV_FILE')
        ]) {
          sh '''
            echo "Loading environment file from Jenkins credentials"
            cp "$ENV_FILE" .env
            chmod 600 .env
          '''
        }
      }
    }

    stage('Build Docker Images') {
      parallel {
        stage('Inventory Service (Laravel)') {
          steps {
            sh '''
              echo "Building image ${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT}"
              docker build -t ${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT} .
            '''
          }
        }
      }
    }

    stage('Prepare Deployment') {
      steps {
        sh '''
          echo "Cleaning host-side caches and logs..."
          rm -f bootstrap/cache/*.php
          rm -f storage/logs/*.log
          rm -rf storage/framework/views/*.php
        '''
      }
    }

    stage('Deploy Latest Containers') {
      steps {
        sh '''
          echo "Deploying commit ${GIT_COMMIT_SHORT}"
          export APP_IMAGE=${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT}

          # Using --remove-orphans ensures old containers not in the new compose file are removed
          docker compose -f docker-compose.yml up -d --remove-orphans

          echo "Verifying deployment..."
          docker ps
        '''
      }
    }

    stage('Post-Deployment Cleanup') {
      steps {
        sh '''
          echo "Pruning old images..."
          docker image prune -f
        '''
      }
    }

  }

  post {
    success {
      echo "Deployment successful (${GIT_COMMIT_SHORT})"
    }
    failure {
      echo "Deployment failed (${GIT_COMMIT_SHORT})"
    }
  }
}
