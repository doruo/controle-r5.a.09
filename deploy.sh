docker network create external
docker plugin install grafana/loki-docker-driver:latest --alias loki --grant-all-permissions
docker plugin enable loki

# Compose all stacks
STACKS="logs traefik monitor dev Prod"
for dir in $STACKS
do
  echo "- Deploying stack: $dir"
  cd "$dir"
  docker compose up -d
  cd ..
done;
