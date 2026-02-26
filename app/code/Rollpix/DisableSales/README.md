# Rollpix_DisableSales

Modulo Magento 2 para deshabilitar temporalmente las ventas en una tienda online sin afectar la navegacion del catalogo. Ideal para situaciones de alta demanda, mantenimiento, o cuando se necesita pausar las compras sin bajar el sitio.

## Compatibilidad

| Requisito | Versiones soportadas |
|---|---|
| PHP | 7.4 ~ 8.3 |
| Magento | 2.4.x (Open Source / Commerce) |

## Instalacion

### Via composer (recomendado)

```bash
composer require rollpix/module-disable-sales
bin/magento module:enable Rollpix_DisableSales
bin/magento setup:upgrade
bin/magento cache:flush
```

### Manual

1. Copiar la carpeta `app/code/Rollpix/DisableSales` en la raiz de tu instalacion Magento.

2. Ejecutar:

```bash
bin/magento module:enable Rollpix_DisableSales
bin/magento setup:upgrade
bin/magento cache:flush
```

---

## Configuracion en el Admin

Ir a **Stores > Configuration > Rollpix > Disable Sales**.

![Configuracion en el Admin](docs/admin-config.jpg)

La configuracion se divide en tres secciones:

### Configuracion General

| Campo | Tipo | Default | Descripcion |
|---|---|---|---|
| **Habilitar** | Si/No | No | Activa o desactiva el bloqueo de ventas |
| **Mensaje** | Textarea | _(ver abajo)_ | Mensaje que se muestra al cliente. Soporta HTML en banner y modal. En errores de checkout/carrito se muestra como texto plano |
| **Deshabilitar tambien el Checkout** | Si/No | Si | Bloquea el acceso al checkout como red de seguridad adicional |

**Mensaje por defecto:**
> Debido a la alta demanda, las compras estan temporalmente suspendidas. Podes seguir navegando el catalogo. Volve pronto!

El campo mensaje acepta **HTML**. Podes usar `<strong>`, `<br>`, `<a href="...">`, etc. El HTML se renderiza en el banner superior y en el modal. Los mensajes de error que aparecen en el checkout y carrito (via Magento message manager) se muestran como texto plano automaticamente.

### Banner Superior (Top Banner)

| Campo | Tipo | Default | Descripcion |
|---|---|---|---|
| **Mostrar Banner Superior** | Si/No | Si | Muestra un banner descartable en la parte superior de la pagina |
| **Color de Fondo** | Color | `#ff6b35` | Color de fondo del banner (formato hex) |
| **Color de Texto** | Color | `#ffffff` | Color del texto del banner (formato hex) |
| **CSS Personalizado del Banner** | Textarea | `font-size: 14px; line-height: 1.4;` | CSS inline aplicado al texto del mensaje. Viene pre-cargado con los valores por defecto para facilitar la edicion |

El banner tiene un boton de cerrar (X). Una vez cerrado, no se vuelve a mostrar en esa sesion del navegador (usa `localStorage`). Se resetea limpiando localStorage o abriendo una ventana de incognito.

### Modal Popup

| Campo | Tipo | Default | Descripcion |
|---|---|---|---|
| **Mostrar Modal** | Si/No | No | Muestra un popup modal una vez por sesion |
| **Color de Fondo** | Color | `#ffffff` | Color de fondo del modal |
| **Color de Texto** | Color | `#333333` | Color del texto del modal |
| **CSS Personalizado del Modal** | Textarea | `font-size: 18px; line-height: 1.6; text-align: center; padding: 10px 20px;` | CSS inline aplicado al texto del mensaje del modal. Viene pre-cargado con los valores por defecto |

El modal se muestra centrado en pantalla, con un ancho maximo de 600px. Aparece una sola vez por sesion del navegador (usa `sessionStorage`). Incluye un boton "Entendido" para cerrarlo.

**Banner y Modal pueden estar activos al mismo tiempo.** Son independientes entre si.

---

## Que hace el modulo cuando esta activo

### 1. Oculta botones "Agregar al Carrito"

Se inyecta CSS inline condicional que oculta los botones `.action.tocart` y `#product-addtocart-button` en:
- Listado de categorias
- Pagina de producto
- Resultados de busqueda
- Widgets de productos

### 2. Bloquea agregar al carrito (backend)

**Primera capa:** Plugin `around` sobre `Magento\Checkout\Controller\Cart\Add::execute`
- Si es una peticion AJAX: responde JSON con `error: true` y el mensaje configurado
- Si es peticion normal: redirect al referer con mensaje de error en message manager

**Segunda capa:** Plugin `before` sobre `Magento\Quote\Model\Quote::addProduct`
- Lanza `LocalizedException` con el mensaje (texto plano)
- Cubre cualquier punto de entrada que use el modelo Quote directamente

### 3. Bloquea el checkout (opcional)

Si "Deshabilitar tambien el Checkout" esta en Si:

- Plugin `around` sobre `Magento\Checkout\Controller\Index\Index::execute`
- Plugin `around` sobre `Magento\Checkout\Controller\Onepage\Index::execute`
- Redirige al carrito con mensaje de error

### 4. Bloquea API REST / GraphQL

Plugin `before` sobre `Magento\Quote\Api\CartItemRepositoryInterface::save`
- Lanza `LocalizedException` bloqueando la creacion de items via API

### 5. Notificacion visual

Muestra el mensaje configurado al usuario via:
- **Banner superior**: fijo en la parte superior, descartable, personalizable en colores y CSS
- **Modal popup**: centrado en pantalla, aparece una vez por sesion, con boton "Entendido"

---

## Comportamiento cuando esta desactivado

Cuando **Habilitar = No**:
- No se ejecuta ninguna logica en los plugins (early return inmediato)
- No se inyecta CSS ni JS
- No se renderizan los templates de banner ni modal
- **Impacto en performance: cero**

El modulo es **100% reversible**: con solo poner Habilitar = No y limpiar cache, todo vuelve a la normalidad. No modifica tablas de base de datos, no crea crons ni observers.

---

## Arquitectura tecnica

### Estructura de archivos

```
app/code/Rollpix/DisableSales/
├── registration.php
├── composer.json
├── etc/
│   ├── module.xml
│   ├── di.xml                          # Plugins globales (API, Quote)
│   ├── config.xml                      # Valores por defecto
│   ├── acl.xml                         # Recurso ACL
│   ├── frontend/
│   │   └── di.xml                      # Plugins frontend (Cart, Checkout)
│   └── adminhtml/
│       └── system.xml                  # Configuracion del admin
├── i18n/
│   └── es_AR.csv                       # Traduccion español Argentina
├── Model/
│   └── Config.php                      # Lectura de configuracion via ScopeConfig
├── Plugin/
│   ├── Cart/
│   │   └── AddPlugin.php              # Bloquea Cart\Add::execute
│   ├── Quote/
│   │   └── AddProductPlugin.php       # Bloquea Quote::addProduct
│   ├── Checkout/
│   │   └── DisableCheckoutPlugin.php  # Bloquea acceso al checkout
│   └── Api/
│       └── CartItemRepositoryPlugin.php # Bloquea API REST/GraphQL
├── ViewModel/
│   └── SalesStatus.php                # Expone config al frontend
├── view/
│   └── frontend/
│       ├── layout/
│       │   └── default.xml            # Inyecta bloques en todas las paginas
│       ├── templates/
│       │   ├── banner.phtml           # Template del banner superior
│       │   └── modal.phtml            # Template del modal popup
│       └── web/
│           └── js/
│               └── disable-sales-modal.js  # JS del modal (RequireJS)
└── README.md
```

### Plugins utilizados

| Plugin | Tipo | Scope | Clase interceptada |
|---|---|---|---|
| AddPlugin | around | frontend | `Magento\Checkout\Controller\Cart\Add` |
| AddProductPlugin | before | global | `Magento\Quote\Model\Quote` |
| DisableCheckoutPlugin | around | frontend | `Magento\Checkout\Controller\Index\Index` / `Onepage\Index` |
| CartItemRepositoryPlugin | before | global | `Magento\Quote\Api\CartItemRepositoryInterface` |

### ViewModel

`Rollpix\DisableSales\ViewModel\SalesStatus` expone al frontend:
- `isDisabled()`: bool
- `getMessage()`: string
- `isBannerEnabled()` / `isModalEnabled()`: bool
- `getBannerBgColor()` / `getBannerTextColor()` / `getBannerCustomCss()`: string
- `getModalBgColor()` / `getModalTextColor()` / `getModalCustomCss()`: string

### ACL

Recurso: `Rollpix_DisableSales::config` bajo `Magento_Config::config`

---

## Guia de testing manual

1. **Activar el modulo** en admin (Habilitar = Si) → Guardar configuracion → Limpiar cache
   - Verificar que los botones "Agregar al Carrito" desaparecen en categorias, producto, busqueda
   - Verificar que aparece el banner superior (si esta habilitado)
   - Verificar que aparece el modal (si esta habilitado)

2. **Intentar agregar al carrito via URL directa** (`/checkout/cart/add/product/ID/`)
   - Verificar que se bloquea y muestra el mensaje de error

3. **Intentar acceder al checkout** con productos en el carrito
   - Verificar que redirige al carrito con mensaje de error

4. **Desactivar el modulo** (Habilitar = No) → Limpiar cache
   - Verificar que todo vuelve a funcionar normalmente

5. **Cambiar mensaje, colores y CSS** → Limpiar cache
   - Verificar que los cambios se reflejan en el frontend

6. **Probar boton cerrar del banner** (X)
   - Verificar que no se muestra de nuevo al navegar (localStorage)

7. **Probar modal en modo incognito**
   - Verificar que aparece una vez y no vuelve a mostrarse en la sesion (sessionStorage)

8. **Probar banner + modal juntos**
   - Activar ambos y verificar que coexisten correctamente

---

## Desinstalacion

```bash
bin/magento module:disable Rollpix_DisableSales
bin/magento setup:upgrade
bin/magento cache:flush
```

Luego eliminar la carpeta `app/code/Rollpix/DisableSales`.

No se crean ni modifican tablas de base de datos. No quedan residuos.

---

## Licencia

MIT
