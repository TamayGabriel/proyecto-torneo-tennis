<?php
namespace App\Enums;

enum EstadoTorneo: string
{
    case CREADO = 'creado';
    case EN_CURSO = 'en curso';
    case FINALIZADO = 'finalizado';
}