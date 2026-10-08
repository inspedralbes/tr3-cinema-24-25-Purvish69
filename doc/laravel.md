# Documentació de les rutes API de FilmGalaxy

## Usuaris

- `http://localhost:8000/api/register` (POST): Registra un nou usuari al sistema.
- `http://localhost:8000/api/login` (POST): Inicia sessió i retorna token d'autenticació.
- `http://localhost:8000/api/logout` (POST): Tanca la sessió de l'usuari (requereix autenticació).
- `http://localhost:8000/api/user` (GET): Llista tots els usuaris.
- `http://localhost:8000/api/user/{id}` (GET): Mostra la informació d'un usuari específic.
- `http://localhost:8000/api/user/{id}` (PUT): Actualitza les dades d'un usuari (requereix autenticació).
- `http://localhost:8000/api/user/{id}` (DELETE): Elimina un usuari (requereix autenticació).
- `http://localhost:8000/api/user/tickets` (GET): Obté els tiquets de l'usuari actual (requereix autenticació).

## Pel·lícules

- `http://localhost:8000/api/movies` (GET): Obté totes les pel·lícules disponibles.
- `http://localhost:8000/api/movies/{id}` (GET): Mostra informació d'una pel·lícula específica.
- `http://localhost:8000/api/movies` (POST): Afegeix una nova pel·lícula (requereix autenticació).
- `http://localhost:8000/api/movies/{id}` (PUT): Actualitza una pel·lícula existent (requereix autenticació).
- `http://localhost:8000/api/movies/{id}` (DELETE): Elimina una pel·lícula (requereix autenticació).

## Sessions de pel·lícules

- `http://localhost:8000/api/sessions` (GET): Llista totes les sessions disponibles.
- `http://localhost:8000/api/sessions/{id}` (GET): Mostra informació d'una sessió específica.
- `http://localhost:8000/api/sessions/{id}/seats` (GET): Mostra els seients disponibles per a una sessió.
- `http://localhost:8000/api/sessions` (POST): Crea una nova sessió (requereix autenticació).
- `http://localhost:8000/api/sessions/{id}` (PUT): Actualitza una sessió existent (requereix autenticació).
- `http://localhost:8000/api/sessions/{id}` (DELETE): Elimina una sessió (requereix autenticació).
- `http://localhost:8000/api/movie-sessions/check-availability` (GET): Comprova disponibilitat de sessions (requereix autenticació).
- `http://localhost:8000/api/sessions/{sessionId}/tickets` (GET): Obté els tiquets venuts per a una sessió específica.

## Seients

- `http://localhost:8000/api/seats` (GET): Llista tots els seients.
- `http://localhost:8000/api/seats/{id}` (GET): Mostra informació d'un seient específic.
- `http://localhost:8000/api/seats` (POST): Crea un nou seient.
- `http://localhost:8000/api/seats/{id}` (PUT): Actualitza un seient existent.
- `http://localhost:8000/api/seats/{id}` (DELETE): Elimina un seient.

## Tiquets

- `http://localhost:8000/api/tickets` (GET): Llista tots els tiquets.
- `http://localhost:8000/api/tickets/{id}` (GET): Mostra informació d'un tiquet específic.
- `http://localhost:8000/api/tickets/{id}/with-user` (GET): Mostra tiquet amb informació de l'usuari.
- `http://localhost:8000/api/tickets/precios-sesion/{sessionId}` (GET): Obté els preus per a una sessió específica.
- `http://localhost:8000/api/tickets` (POST): Crea un nou tiquet.
- `http://localhost:8000/api/tickets/{id}` (PUT): Actualitza un tiquet existent.
- `http://localhost:8000/api/tickets/{id}` (DELETE): Elimina un tiquet.
- `http://localhost:8000/api/tickets/user/{userId}/complete` (GET): Obté tots els tiquets complets d'un usuari.
- `http://localhost:8000/api/tickets/send-email/{userId}/{sessionId}` (POST): Envia els tiquets per correu electrònic.

## Pagaments

- `http://localhost:8000/api/payments` (GET): Llista tots els pagaments.
- `http://localhost:8000/api/payments/{id}` (GET): Mostra informació d'un pagament específic.
- `http://localhost:8000/api/payments` (POST): Registra un nou pagament.
- `http://localhost:8000/api/payments/{id}` (PUT): Actualitza un pagament existent.
- `http://localhost:8000/api/payments/{id}` (DELETE): Elimina un pagament.
