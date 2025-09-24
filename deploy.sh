sudo docker network create external;

# Compose all stacks
STACKS="logs traefik monitor dev Prod"
for dir in $STACKS
do
  echo "- Deploying stack: $dir"
  cd "$dir"
  sudo docker compose up -d
  cd ..
done;