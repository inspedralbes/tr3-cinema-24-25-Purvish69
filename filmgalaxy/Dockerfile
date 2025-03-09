FROM node:18-alpine

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

# Comando para iniciar el servidor de Nuxt
CMD ["npm", "run", "dev", "--", "--host"]
