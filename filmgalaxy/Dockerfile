FROM node:18

WORKDIR /app

COPY package*.json ./

RUN rm -f package-lock.json && npm install


COPY . .

EXPOSE 3000

CMD [ "npm", "run", "dev", "--", "--host=0.0.0.0" ]

# CMD [ "npm", "run", "serve" ]