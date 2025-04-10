<?php
// lang.php

$lang = $_GET['lang'] ?? 'pt';

$textos = [
    'pt' => [
        'titulo'        => 'Mapeamento de Serviços Públicos e Privados',
        'bem_vindo'     => 'Bem-vindo',
        'buscar'        => 'Buscar',
        'agendar'       => 'Agendar',
        'agendado'      => 'Agendado',
        'cancelar'      => 'Cancelar Agendamento',
        'indisponivel'  => 'Vaga Indisponível',
        'detalhes'      => 'Ver detalhes',
        'como_agendar'  => 'Como agendar:',
        'horario'       => 'Horário de Funcionamento',
        'descricao'     => 'Descrição',
        'rota'          => 'Traçar rota',
        'navegar'       => 'Navegar',
        'compartilhar'  => 'Compartilhar',
    ],
    'es' => [
        'titulo'        => 'Mapeo de Servicios Públicos y Privados',
        'bem_vindo'     => 'Bienvenido',
        'buscar'        => 'Buscar',
        'agendar'       => 'Reservar',
        'agendado'      => 'Reservado',
        'cancelar'      => 'Cancelar reserva',
        'indisponivel'  => 'No disponible',
        'detalhes'      => 'Ver detalles',
        'como_agendar'  => 'Cómo agendar:',
        'horario'       => 'Horario de atención',
        'descricao'     => 'Descripción',
        'rota'          => 'Trazar ruta',
        'navegar'       => 'Navegar',
        'compartilhar'  => 'Compartir',
    ],
    'en' => [
        'titulo'        => 'Mapping Public and Private Services',
        'bem_vindo'     => 'Welcome',
        'buscar'        => 'Search',
        'agendar'       => 'Book',
        'agendado'      => 'Booked',
        'cancelar'      => 'Cancel Booking',
        'indisponivel'  => 'Unavailable',
        'detalhes'      => 'View details',
        'como_agendar'  => 'How to book:',
        'horario'       => 'Working Hours',
        'descricao'     => 'Description',
        'rota'          => 'Get Directions',
        'navegar'       => 'Navigate',
        'compartilhar'  => 'Share',
    ]
];

$t = $textos[$lang] ?? $textos['pt']; // Fallback para português
