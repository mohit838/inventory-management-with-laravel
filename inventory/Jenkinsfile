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
    CONTAINER_NAME = 'inventory_app'
    BACKUP_CONTAINER_NAME = 'inventory_app_backup'
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
          
          # Tag as latest for easy reference
          docker tag ${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT} ${IMAGE_PREFIX}-inventory:latest
        '''
      }
    }

    stage('Backup Current Container') {
      steps {
        sh '''
          set +e
          # Check if current container exists
          if docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
            echo "Backing up current container as ${BACKUP_CONTAINER_NAME}"
            docker rename ${CONTAINER_NAME} ${BACKUP_CONTAINER_NAME} || true
          else
            echo "No existing container to backup"
          fi
        '''
      }
    }

    stage('Deploy New Container') {
      steps {
        sh '''
          set -e
          export APP_IMAGE=${IMAGE_PREFIX}-inventory:${GIT_COMMIT_SHORT}
          export GIT_COMMIT_SHORT=${GIT_COMMIT_SHORT}

          # Deploy new container
          docker compose --env-file .env -f docker-compose.yml up -d --remove-orphans

          echo "New container deployed successfully"
          docker ps --filter "name=${CONTAINER_NAME}"
        '''
      }
    }

    stage('Health Check') {
      steps {
        sh '''
          set -e
          echo "Waiting for container to be healthy..."
          
          # Wait up to 60 seconds for health check to pass
          TIMEOUT=60
          ELAPSED=0
          INTERVAL=5
          
          while [ $ELAPSED -lt $TIMEOUT ]; do
            HEALTH_STATUS=$(docker inspect --format='{{.State.Health.Status}}' ${CONTAINER_NAME} 2>/dev/null || echo "unknown")
            
            echo "Health status: $HEALTH_STATUS (elapsed: ${ELAPSED}s)"
            
            if [ "$HEALTH_STATUS" = "healthy" ]; then
              echo "Container is healthy!"
              exit 0
            fi
            
            if [ "$HEALTH_STATUS" = "unhealthy" ]; then
              echo "Container became unhealthy!"
              docker logs --tail=50 ${CONTAINER_NAME}
              exit 1
            fi
            
            sleep $INTERVAL
            ELAPSED=$((ELAPSED + INTERVAL))
          done
          
          echo "Health check timeout after ${TIMEOUT}s"
          docker logs --tail=50 ${CONTAINER_NAME}
          exit 1
        '''
      }
    }

    stage('Cleanup Old Container') {
      steps {
        sh '''
          set +e
          # Remove backup container if deployment was successful
          if docker ps -a --format '{{.Names}}' | grep -q "^${BACKUP_CONTAINER_NAME}$"; then
            echo "Removing old backup container"
            docker stop ${BACKUP_CONTAINER_NAME} || true
            docker rm ${BACKUP_CONTAINER_NAME} || true
          fi
        '''
      }
    }

    stage('Cleanup Old Images') {
      steps {
        sh '''
          set +e
          echo "Cleaning up old images (keeping last 3 versions)..."
          
          # Keep only the 3 most recent images
          docker images ${IMAGE_PREFIX}-inventory --format "{{.Tag}}" | \
            grep -v "latest" | \
            tail -n +4 | \
            xargs -I {} docker rmi ${IMAGE_PREFIX}-inventory:{} || true
          
          # Prune dangling images
          docker image prune -f || true
        '''
      }
    }
  }

  post {
    failure {
      echo "Deployment failed. Attempting rollback..."
      sh '''
        set +e
        
        # Show logs from failed container
        echo "=== Failed Container Logs ==="
        docker logs --tail=100 ${CONTAINER_NAME} || true
        
        # Stop and remove failed container
        docker stop ${CONTAINER_NAME} || true
        docker rm ${CONTAINER_NAME} || true
        
        # Restore backup if it exists
        if docker ps -a --format '{{.Names}}' | grep -q "^${BACKUP_CONTAINER_NAME}$"; then
          echo "Restoring backup container..."
          docker rename ${BACKUP_CONTAINER_NAME} ${CONTAINER_NAME} || true
          docker start ${CONTAINER_NAME} || true
          echo "Rollback complete"
        else
          echo "No backup container available for rollback"
        fi
        
        docker ps
      '''
    }

    always {
      echo "Cleanup workspace..."
      sh '''
        set +e
        # Clean up workspace but keep .env for debugging if needed
        rm -f .env || true
      '''
      cleanWs()
    }

    success {
      echo "Deployment successful! Version: ${GIT_COMMIT_SHORT}"
      sh '''
        echo "=== Deployed Container Info ==="
        docker ps --filter "name=${CONTAINER_NAME}" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
        
        echo ""
        echo "=== Container Labels ==="
        docker inspect ${CONTAINER_NAME} --format='{{range $k, $v := .Config.Labels}}{{$k}}={{$v}}{{println}}{{end}}'
      '''
    }
  }
}
