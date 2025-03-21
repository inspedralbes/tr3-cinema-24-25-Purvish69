FROM node:18-alpine

WORKDIR /app

COPY package*.json ./

# Remueve el package-lock y ejecuta npm install sin dependencias opcionales
RUN rm -f package-lock.json && npm install --no-optional

COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host"]