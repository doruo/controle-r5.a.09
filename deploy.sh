sudo docker network create external
sudo docker plugin install grafana/loki-docker-driver:latest --alias loki --grant-all-permissions
sudo docker plugin enable loki

# Compose all stacks
STACKS="logs traefik monitor dev Prod"
for dir in $STACKS
do
  echo "- Deploying stack: $dir"
  cd "$dir"
  sudo docker compose up -d
  cd ..
done;
