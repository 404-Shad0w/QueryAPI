---

# 🌐 QueryAPI

Una API avanzada y estática para consultar información en tiempo real de **servidores externos de Minecraft: Bedrock Edition**. Diseñada para obtener datos como jugadores conectados, MOTD, estado online/offline, modo de juego, puertos y más.

---

## 📦 Requisitos

- 🧠 PHP 8.1 o superior  
- ⚙️ PocketMine-MP 5.x  
- 🌍 Servidores Bedrock accesibles desde el host del plugin  

---

## 🚀 Instalación

> ⚠️ Esta API **no es un plugin independiente**. Debes incluirla dentro de tu propio plugin como dependencia.

1. Descarga o clona este repositorio.
2. Copia la carpeta `QueryAPI/` dentro del directorio `src/` de tu plugin:

```
plugins/
└── TuPlugin/
    └── src/
        └── QueryAPI/
```

3. Llama a `QueryAPI::init();` al iniciar tu plugin:

```php
use QueryAPI\QueryAPI;

public function onEnable(): void {
    QueryAPI::init();
}
```

---

## 🧠 ¿Qué hace esta API?

- Envía un paquete UDP (RakNet) a un servidor externo Bedrock.
- Interpreta la respuesta para extraer información como jugadores, motd, puertos y más.
- Devuelve un array con información legible y organizada.
- Detecta si el servidor está **offline** o no responde.

---

## 🛠️ Ejemplo de uso

```php
use QueryAPI\QueryAPI;

QueryAPI::init();

$status = QueryAPI::query("play.example.net", 19132);

if ($status["Status"] === "online") {
    echo "Servidor online\n";
    echo "MOTD: {$status["Motd"]}\n";
    echo "Jugadores: {$status["OnlinePlayers"]}/{$status["MaxPlayers"]}\n";
    echo "Modo de juego: {$status["Gamemode"]}\n";
} else {
    echo "Servidor offline";
}
```

---

## 📋 Métodos disponibles

| Método                                                        | Tipo       | Descripción                                                                 |
|---------------------------------------------------------------|------------|-----------------------------------------------------------------------------|
| `init()`                                                      | `void`     | Inicializa la API. Debes llamarlo antes de usar `query()`.                 |
| `query(string $ip, int $port, int $timeout = 3)`              | `array`    | Consulta el servidor externo. Devuelve un array con la información o `"offline"`. |

---

## 📊 Estructura del array de respuesta

```php
[
  "Status" => "online" | "offline",
  "Motd" => string,
  "Version" => string,
  "OnlinePlayers" => int,
  "MaxPlayers" => int,
  "World" => string,
  "Gamemode" => string,
  "IsWhitelisted" => bool,
  "PortIPv4" => int,
  "PortIPv6" => int
]
```

> ⚠️ Si el servidor no responde, el array solo contendrá:
```php
["Status" => "offline"]
```

---

## 🧪 Ejemplo de verificación segura

```php
$status = QueryAPI::query("1.2.3.4", 19132);

if ($status["Status"] !== "online") {
    $player->sendMessage("❌ El servidor está apagado.");
    return;
}

$online = $status["OnlinePlayers"];
$max = $status["MaxPlayers"];

$player->sendMessage("🎮 $online/$max jugadores conectados.");
```

---

## 🧾 Casos de uso recomendados

- Sistemas de **lobby con estado de servidores**.
- Plugins de **selector de servidores externos**.
- Monitoreo de servidores hermanos desde el panel.
- API de estado para webs o integraciones externas.

---

## 👤 Autor

* 👨‍💻 Creador: `404_Shad0w`  
* 💬 Discord: [Click aquí](https://discord.com/users/1177436591761932328)

---

## 📝 Licencia

Este proyecto está bajo la licencia **MIT**. Puedes usarlo, modificarlo y distribuirlo libremente en tus propios plugins.

---