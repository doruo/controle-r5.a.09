sudo docker network create external;

# Compose all stacks
STACKS="logs traefik monitor dev prod"
for dir in $STACKS
do
  echo "- Deploying stack: $dir"
  cd "$dir"
  sudo docker compose up -d
  cd ..
done;