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
        sh '''
          set -e
          git config --global --add safe.directory "$WORKSPACE"
          echo "Commit full:  $(git rev-parse HEAD)"
          echo "Commit short: $(git rev-parse --short=7 HEAD)"
        '''
        script {
          env.GIT_COMMIT_FULL = sh(script: 'git rev-parse HEAD', returnStdout: true).trim()
          env.GIT_COMMIT_SHORT = sh(script: 'git rev-parse --short=7 HEAD', returnStdout: true).trim()
        }
      }
    }

    stage('Prepare Docker') {
      steps {
        sh '''
          set -e
          mkdir -p "$DOCKER_CONFIG"
          docker version

          # Ensure docker compose exists (Compose V2 plugin)
          if ! docker compose version >/dev/null 2>&1; then
            echo "docker compose not found. Installing compose plugin..."
            mkdir -p /usr/local/lib/docker/cli-plugins
            apk add --no-cache curl
            curl -fsSL -o /usr/local/lib/docker/cli-plugins/docker-compose \
              https://github.com/docker/compose/releases/download/v2.29.2/docker-compose-linux-x86_64
            chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
          fi

          docker compose version
        '''
      }
    }

    stage('Load Environment File') {
      steps {
        withCredentials([file(credentialsId: 'laravel-inv', variable: 'ENV_FILE')]) {
          sh '''
            set -e
            cp "$ENV_FILE" .env
            chmod 600 .env
            echo "Loaded .env to $(pwd)/.env"
          '''
        }
      }
    }

    stage('Build Docker Image') {
      steps {
        sh '''
          set -e
          echo "Building image ${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT}"
          docker build -t ${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT} .
        '''
      }
    }

    stage('Deploy (Compose)') {
      steps {
        sh '''
          set -e
          export APP_IMAGE=${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT}

          # Bring up MySQL + Redis (if you run them separately, keep these two)
          # docker compose --env-file .env -f docker-compose.mysql.yml up -d
          # docker compose --env-file .env -f docker-compose.redis.yml up -d

          # Bring up app
          docker compose --env-file .env -f docker-compose.yml up -d --remove-orphans

          echo "Containers running:"
          docker ps
        '''
      }
    }
  }

  post {
    failure {
      echo "Deployment failed. Showing logs..."
      sh '''
        set +e
        docker ps
        echo "=== App logs (tail) ==="
        docker logs --tail=200 inventory_app || true
        echo "=== MySQL logs (tail) ==="
        docker logs --tail=200 inventory_mysql || true
        echo "=== Redis logs (tail) ==="
        docker logs --tail=200 inventory_redis || true
      '''
    }

    always {
      echo "Cleanup..."
      sh '''
        set +e
        docker image prune -af --filter "until=24h" || true
      '''
      cleanWs()
    }

    success {
      echo "Deployment successful (${GIT_COMMIT_SHORT})"
    }
  }
}
