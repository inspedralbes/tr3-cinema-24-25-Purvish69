export const useAuth = () => {
    const API_URL = 'http://localhost:8000/api'

    // obtener todas las peliculas
    const getPeliculas = async () => {
        try {
            const response = await fetch(`${API_URL}/movies`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include'
            });
            
            if (!response.ok) {
                const data = await response.json();
                console.error('Server validation error:', data);
                return { error: data.message || 'Error de autenticación' };
            }
            
            const data = await response.json();
            console.log('Peliculas recibidas para pelicula:', data);
            return data;
        } catch (error) {
            console.error('Error en la solicitud:', error);
            return { error: 'Error de conexión' };
        }
    };

    return {
        getPeliculas
    };
};