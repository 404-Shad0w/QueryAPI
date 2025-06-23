<?php

namespace QueryAPI;

use Socket;

class QueryAPI
{
    private const RAKNET_MAGIC = "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";
    private static bool $initialized = false;

    public static function init(): void
    {
        self::$initialized = true;
    }

    private static function checkInit(): void
    {
        if (!self::$initialized) {
            trigger_error("QueryAPI no ha sido inicializada. Llama a QueryAPI::init() primero.", E_USER_WARNING);
        }
    }

    public static function query(string $ip, int $port, int $timeout = 3): array
    {
        self::checkInit();

        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return ["Status" => "offline"];
        }

        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ["sec" => $timeout, "usec" => 0]);
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ["sec" => $timeout, "usec" => 0]);

        $clientGUID = random_int(0, PHP_INT_MAX) * 2 + random_int(0, 1);

        $ping = "\x01"
            . pack("J", (int)(microtime(true) * 1000))
            . self::RAKNET_MAGIC
            . pack("J", $clientGUID);

        $sent = socket_sendto($socket, $ping, strlen($ping), 0, $ip, $port);
        if ($sent === false) {
            socket_close($socket);
            return ["Status" => "offline"];
        }

        $buf = "";
        $from = "";
        $port = 0;
        $bytes = socket_recvfrom($socket, $buf, 2048, 0, $from, $port);
        socket_close($socket);

        if ($bytes === false || $bytes === 0 || ord($buf[0]) !== 0x1C) {
            return ["Status" => "offline"];
        }

        $data = substr($buf, 35);
        return self::formatResponse($data);
    }

    private static function formatResponse(string $data): array
    {
        $parts = explode(";", $data);

        return [
            "Status" => "online",
            "Motd" => $parts[1] ?? "N/A",
            "Version" => $parts[3] ?? "N/A",
            "OnlinePlayers" => (int)($parts[4] ?? 0),
            "MaxPlayers" => (int)($parts[5] ?? 0),
            "World" => $parts[7] ?? "N/A",
            "Gamemode" => $parts[8] ?? "N/A",
            "IsWhitelisted" => str_contains(strtolower($parts[1] ?? ""), "whitelist"),
            "PortIPv4" => (int)($parts[10] ?? 0),
            "PortIPv6" => (int)($parts[11] ?? 0),
        ];
    }
}