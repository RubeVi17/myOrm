# 🗄️ myOrm

Un ORM (Object-Relational Mapping) educativo y ligero construido desde cero en PHP para aprender los fundamentos de la persistencia de datos y el patrón Active Record.

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![Status](https://img.shields.io/badge/status-en%20desarrollo-yellow)

## 📋 Tabla de Contenidos

- [Características](#-características)
- [¿Por qué myOrm?](#-por-qué-myorm)
- [Instalación](#-instalación)
- [Configuración](#️-configuración)
- [Uso Básico](#-uso-básico)
- [Panel de Administración](#-panel-de-administración)
- [Migraciones](#-migraciones)
- [Query Builder](#-query-builder)
- [Relaciones](#-relaciones)
- [CLI](#️-cli)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Roadmap](#-roadmap)
- [Contribuir](#-contribuir)
- [Autor](#-autor)

## ✨ Características

- ✅ **CRUD Completo** - Create, Read, Update, Delete
- ✅ **Query Builder** - Construcción fluida de consultas SQL
- ✅ **Migraciones** - Control de versiones para tu base de datos
- ✅ **Relaciones** - HasMany, BelongsTo, BelongsToMany
- ✅ **Timestamps Automáticos** - `created_at` y `updated_at`
- ✅ **Fillable/Guarded** - Protección contra asignación masiva
- ✅ **SQL Debug** - Visualiza las queries generadas
- ✅ **Panel Web** - Interfaz gráfica para gestionar modelos
- ✅ **CLI** - Comandos de consola para automatizar tareas
- ✅ **Foreign Keys** - Soporte para llaves foráneas

## 🤔 ¿Por qué myOrm?

Este proyecto nació como un laboratorio de aprendizaje para:

- Entender **cómo funcionan los ORMs** por dentro
- Aprender sobre **patrones de diseño** (Active Record, Builder, Factory)
- Dominar **SQL** y la gestión de bases de datos
- Practicar **arquitectura de software** limpia y mantenible
- No depender de "magia" en frameworks como Laravel/Eloquent

> **Nota:** Este ORM es educativo actualmente en desarrollo y no está recomendado para producción. ¡Úsalo para aprender y experimentar!

## 📦 Instalación

### Requisitos

- PHP >= 8.0
- MySQL >= 5.7
- Extensión PDO habilitada

### Pasos
```bash
# Clonar el repositorio
git clone https://github.com/RubeVi17/myOrm.git
cd myOrm

# Configurar base de datos
# Edita Core/Database.php con tus credenciales

# Ejecutar migraciones
php migrate.php

# Iniciar servidor de desarrollo
php -S localhost:8000 -t panel/
```

Accede a `http://localhost:8000` para usar el panel de administración.

## ⚙️ Configuración

Edita el archivo `Core/Database.php`:
```php
<?php
class Database
{
    private static $host = 'localhost';
    private static $dbname = 'myorm_db';
    private static $username = 'root';
    private static $password = '';
    
    // ...
}
```

## 🚀 Uso Básico

### Definir un Modelo
```php
<?php
// Models/Product.php

class Product extends Model
{
    protected static string $table = 'products';
    
    protected array $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];
}
```

### Crear Registros
```php
// Crear un producto
$product = Product::create([
    'name' => 'Laptop',
    'description' => 'High-performance laptop',
    'price' => 999.99,
    'stock' => 50
]);

echo $product->id; // Auto-generado
```

### Leer Registros
```php
// Obtener todos los productos
$products = Product::all();

// Buscar por ID
$product = Product::find(1);

// Buscar con condiciones
$expensiveProducts = Product::where('price', '>', 500)->get();

// Primer resultado
$firstProduct = Product::where('stock', '>', 0)->first();
```

### Actualizar Registros
```php
$product = Product::find(1);
$product->price = 899.99;
$product->save();

// O con update directo
$product->update([
    'price' => 899.99,
    'stock' => 45
]);
```

### Eliminar Registros
```php
$product = Product::find(1);
$product->delete();
```

## 🎨 Panel de Administración

myOrm incluye un panel web moderno con interfaz para gestionar tus modelos sin escribir código.

### Características del Panel

- 📊 **Dashboard** - Estadísticas y acceso rápido
- 🗄️ **Migraciones** - Ejecutar y ver migraciones
- ✨ **Constructor Visual** - Crear modelos y migraciones visualmente
- 🔍 **Query Builder** - Consultar datos con filtros
- ➕ **Crear Registros** - Formularios dinámicos
- ✏️ **Actualizar Registros** - Edición inline
- 👁️ **Vista Detallada** - Ver registros con relaciones

### Capturas

![Dashboard](docs/screenshots/dashboard.png)
![Crear Modelo](docs/screenshots/create-model.png)

## 📝 Migraciones

### Crear una Migración

Usando CLI:
```bash
php orm make:migration CreateProductsTable
```

O desde el panel web: `Migraciones > Crear Nuevo`

### Ejemplo de Migración
```php
<?php
// Migrations/CreateProductsTable.php

return new class{
    public function up()
    {
        Schema::create('users', function($table){
            $table->id();
            $table->string('name');
            $table->string('email', 150);
            $table->integer('age');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::drop('users');
    }
}
```

### Ejecutar Migraciones
```bash
php migrate.php
```

## 🔨 Query Builder

El Query Builder permite construir consultas SQL de forma fluida:
```php
// WHERE simple
$products = Product::where('price', '>', 100)->get();

// WHERE con múltiples condiciones
$products = Product::where('price', '>', 100)
                   ->where('stock', '>', 0)
                   ->get();

// Operadores soportados: =, !=, >, <, >=, <=

// Debug SQL
$builder = Product::where('price', '>', 100);
echo $builder->sqlDebug(); // SELECT * FROM products WHERE price > 100

// Primer resultado
$product = Product::where('stock', '>', 0)->first();
```

## 🔗 Relaciones

### HasMany (Uno a Muchos)
```php
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}

// Uso
$user = User::find(1);
$posts = $user->posts()->get();
```

### BelongsTo (Pertenece a)
```php
class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

// Uso
$post = Post::find(1);
$author = $post->user()->first();
```

### Eager Loading
```php
$topic = Topic::find(1);

// Cargar múltiples relaciones
$topic->loadMany([
    'user',
    'comments.user',
    'likes.user'
]);

// Acceder a datos relacionados
echo $topic->user->name;
foreach ($topic->comments as $comment) {
    echo $comment->user->name . ': ' . $comment->comment;
}
```

## 🖥️ CLI

myOrm incluye comandos de terminal para automatizar tareas:
```bash
# Crear un modelo
php orm make:model Product

# Crear una migración
php orm make:migration CreateProductsTable

# Ejecutar migraciones
php orm migrate

# Iniciar servidor (requiere modificar el script)
php orm serve
```

### Personalizar CLI

Edita el archivo `orm` en la raíz del proyecto para agregar más comandos.

## 📁 Estructura del Proyecto
```
myOrm/
├── Core/
│   ├── Database.php          # Conexión PDO
│   ├── Model.php              # Clase base Model
│   ├── QueryBuilder.php       # Constructor de queries
│   └── Migration.php          # Sistema de migraciones
├── Models/
│   ├── User.php
│   ├── Post.php
│   └── ...
├── Migrations/
│   ├── CreateUsersTable.php
│   └── ...
├── Panel/
│   ├── index.php              # Dashboard
│   ├── create.php             # Crear registros
│   ├── query.php              # Query builder
│   ├── update.php             # Actualizar registros
│   ├── migrate.php            # Gestión de migraciones
│   ├── view.php               # Vista detallada
│   └── layout.php             # Layout principal
├── migrate.php                # Ejecutor de migraciones
├── orm                        # CLI script
└── README.md
```

## 🗺️ Roadmap

### En Desarrollo
- [ ] Soft Deletes (`deleted_at`)
- [ ] Scopes reutilizables
- [ ] Validación de datos
- [ ] Seeders para datos de prueba
- [ ] Paginación
- [ ] Observers/Events

### Planeado
- [ ] Caché de queries
- [ ] Relaciones polimórficas
- [ ] Transactions
- [ ] Soporte para SQLite
- [ ] API REST automática
- [ ] Generador de documentación

### Ideas Futuras
- [ ] GraphQL support
- [ ] Multi-tenancy
- [ ] Audit logging
- [ ] Full-text search

## 🤝 Contribuir

Este es un proyecto educativo personal, pero las contribuciones son bienvenidas:

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Guías de Estilo

- Usa PSR-12 para código PHP
- Comenta código complejo
- Escribe tests si es posible
- Actualiza la documentación

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT. Ver `LICENSE` para más detalles.

## 👨‍💻 Autor

**RubeVi17**

- Forbidden 403

### Proyectos Relacionados

- AgroFlux
- LogiFlux
- Forge by CRS Software
- SIGA

---

## 🙏 Agradecimientos

- Inspirado por Laravel Eloquent
- Diseño del panel inspirado en Strapi CMS
- Comunidad PHP por las mejores prácticas

---

## 📚 Recursos de Aprendizaje

Si estás aprendiendo sobre ORMs, estos recursos pueden ayudarte:

- [Active Record Pattern](https://en.wikipedia.org/wiki/Active_record_pattern)
- [Laravel Eloquent Documentation](https://laravel.com/docs/eloquent)
- [PDO PHP Manual](https://www.php.net/manual/en/book.pdo.php)
- [Database Design Best Practices](https://www.sqlshack.com/learn-sql-database-design/)

---

## 💬 Feedback

¿Encontraste un bug? ¿Tienes una sugerencia? [Abre un issue](https://github.com/RubeVi17/myOrm/issues)

---



**⭐ Si este proyecto te ayudó a aprender, considera darle una estrella ⭐**

Hecho con ❤️ y ☕

