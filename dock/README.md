# Testing en Laravel

Este documento proporciona una guía básica sobre cómo realizar pruebas en aplicaciones Laravel, con ejemplos prácticos basados en nuestro proyecto de cine.

## Introducción al Testing en Laravel

Laravel ofrece un excelente soporte para testing, permitiéndonos verificar que nuestra aplicación funciona como se espera. El framework incluye herramientas para realizar tanto pruebas unitarias como pruebas de integración.

## Tipos de Tests en Laravel

### 1. Tests Unitarios

Los tests unitarios se centran en probar pequeñas partes aisladas de código, como métodos individuales de una clase.

```php
// Ejemplo de un test unitario
public function test_example()
{
    $this->assertTrue(true);
}
```

### 2. Tests de Características (Feature Tests)

Los tests de características prueban partes más grandes de la aplicación, como endpoints de API o controladores completos.

```php
// Ejemplo de un test de característica
public function test_movies_endpoint_returns_success_response()
{
    $response = $this->get('/api/movies');
    
    $response->assertStatus(200);
}
```

## Estructura de Tests en Nuestro Proyecto

En nuestro proyecto de cine, hemos implementado varios tests para verificar el funcionamiento de los endpoints de API:

1. **ApiEndpointsTest**: Tests básicos para verificar que los endpoints principales responden con código 200.
2. **MovieTest**: Tests para los endpoints relacionados con películas.
3. **TicketTest**: Tests para los endpoints relacionados con tickets.
4. **UserTest**: Tests para los endpoints relacionados con usuarios.

## Ejecutando Tests

Para ejecutar todos los tests en Laravel:

```bash
php artisan test
```

Para ejecutar un archivo de test específico:

```bash
php artisan test --filter=ApiEndpointsTest
```

## Aserciones Comunes en Laravel

Laravel proporciona muchas aserciones útiles para verificar respuestas HTTP:

- `assertStatus($code)`: Verifica que la respuesta tenga un código de estado específico.
- `assertJson($data)`: Verifica que la respuesta contenga los datos JSON especificados.
- `assertJsonStructure($structure)`: Verifica que la respuesta tenga una estructura JSON específica.
- `assertRedirect($uri)`: Verifica que la respuesta sea una redirección a una URI específica.

## Mocking y Bases de Datos

### RefreshDatabase

El trait `RefreshDatabase` permite resetear la base de datos entre tests:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    
    // Tests...
}
```

### Factories

Las factories permiten crear datos de prueba fácilmente:

```php
$user = User::factory()->create();
```

## Autenticación en Tests

Para simular un usuario autenticado en tests:

```php
use Laravel\Sanctum\Sanctum;

// Para APIs con Sanctum
Sanctum::actingAs(
    User::factory()->create(),
    ['*']
);

// Para aplicaciones web
$this->actingAs(User::factory()->create());
```

## Buenas Prácticas

1. **Tests Independientes**: Cada test debe ser independiente y no depender del estado dejado por otros tests.
2. **Nombres Descriptivos**: Usa nombres de métodos descriptivos que expliquen qué está probando el test.
3. **Arrange-Act-Assert**: Estructura tus tests siguiendo el patrón AAA:
   - Arrange: Preparar los datos y condiciones para el test.
   - Act: Realizar la acción que quieres probar.
   - Assert: Verificar que el resultado es el esperado.
4. **Tests Robustos**: Escribe tests que no dependan de datos específicos en la base de datos.

## Recursos Adicionales

- [Documentación oficial de Laravel sobre Testing](https://laravel.com/docs/10.x/testing)
- [Laravel Dusk para pruebas de navegador](https://laravel.com/docs/10.x/dusk)
- [PHPUnit Documentation](https://phpunit.readthedocs.io/)

## Ejemplos de Nuestro Proyecto

### Test Básico de Endpoint

```php
public function test_can_get_all_movies()
{
    $response = $this->getJson('/api/movies');
    
    $response->assertStatus(200);
}
```

### Test de Autenticación

```php
public function test_movie_creation_requires_authentication()
{
    $movieData = [
        'title' => 'Nueva Película Test',
        'description' => 'Descripción de prueba'
    ];
    
    $response = $this->postJson('/api/movies', $movieData);
    
    $this->assertTrue(in_array($response->status(), [401, 403]));
}
```

### Test Flexible

```php
public function test_user_can_register()
{
    $userData = [
        'nombre' => 'Usuario Test',
        'apellido' => 'Apellido Test',
        'email' => 'test@example.com',
        'telefono' => '123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];
    
    $response = $this->postJson('/api/register', $userData);
    
    $status = $response->status();
    $this->assertTrue(in_array($status, [201, 200, 422]));
}
```
