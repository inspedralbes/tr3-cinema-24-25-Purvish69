FROM node:18
FROM node:18

WORKDIR /app

# Don't run npm install in the Dockerfile
# We'll handle this in the docker-compose command

EXPOSE 3000

CMD ["sh", "-c", "npm install && npm run dev -- --host=0.0.0.0"]