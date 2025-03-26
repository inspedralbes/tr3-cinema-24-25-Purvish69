FROM node:18

WORKDIR /app

# Don't run npm install in the Dockerfile
# We'll handle this in the docker-compose command

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host"]