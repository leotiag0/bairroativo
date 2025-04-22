<?php
// Configuração de segurança
header('Content-Type: text/html; charset=utf-8');

// Idiomas suportados
$supported_langs = ['pt', 'es', 'en'];

// Obter idioma com fallback seguro
$lang = 'pt'; // Idioma padrão

if (isset($_GET['lang']) {
    $requested_lang = substr($_GET['lang'], 0, 2); // Limita a 2 caracteres
    if (in_array($requested_lang, $supported_langs)) {
        $lang = $requested_lang;
    }
}

// Definir cookie de idioma por 30 dias
setcookie('lang', $lang, time() + (86400 * 30), '/');

// Função de tradução com fallback
function translate($key, $lang, $default = '') {
    global $textos;
    
    // Verifica se o texto existe no idioma solicitado
    if (isset($textos[$lang][$key])) {
        return $textos[$lang][$key];
    }
    
    // Fallback para português
    if ($lang !== 'pt' && isset($textos['pt'][$key])) {
        return $textos['pt'][$key];
    }
    
    // Fallback genérico
    return $default !== '' ? $default : $key;
}

// Textos traduzidos
$textos = [
    'pt' => [
        'titulo' => 'Mapeamento de Serviços Públicos e Privados',
        'slogan' => 'Encontre serviços próximos a você',
        'bem_vindo' => 'Bem-vindo',
        'buscar' => 'Buscar',
        'agendar' => 'Agendar',
        'agendado' => 'Agendado',
        'cancelar' => 'Cancelar Agendamento',
        'indisponivel' => 'Vaga Indisponível',
        'detalhes' => 'Ver detalhes',
        'como_agendar' => 'Como agendar:',
        'horario' => 'Horário de Funcionamento',
        'descricao' => 'Descrição',
        'rota' => 'Traçar rota',
        'navegar' => 'Navegar',
        'compartilhar' => 'Compartilhar',
        'acessibilidade' => 'Acessibilidade',
        'contraste' => 'Alto Contraste',
        'modo_escuro' => 'Modo Escuro',
        'tamanho_fonte' => 'Tamanho da Fonte',
        'portugues' => 'Português',
        'espanhol' => 'Espanhol',
        'ingles' => 'Inglês',
        'servicos_proximos' => 'Serviços Próximos',
        'todos_servicos' => 'Todos os Serviços',
        'mapa' => 'Mapa Interativo',
        'lista' => 'Lista Completa'
    ],
    'es' => [
        'titulo' => 'Mapeo de Servicios Públicos y Privados',
        'slogan' => 'Encuentra servicios cerca de ti',
        'bem_vindo' => 'Bienvenido',
        'buscar' => 'Buscar',
        'agendar' => 'Reservar',
        'agendado' => 'Reservado',
        'cancelar' => 'Cancelar reserva',
        'indisponivel' => 'No disponible',
        'detalhes' => 'Ver detalles',
        'como_agendar' => 'Cómo agendar:',
        'horario' => 'Horario de atención',
        'descricao' => 'Descripción',
        'rota' => 'Trazar ruta',
        'navegar' => 'Navegar',
        'compartilhar' => 'Compartir',
        'acessibilidade' => 'Accesibilidad',
        'contraste' => 'Alto Contraste',
        'modo_escuro' => 'Modo Oscuro',
        'tamanho_fonte' => 'Tamaño de Fuente',
        'portugues' => 'Portugués',
        'espanhol' => 'Español',
        'ingles' => 'Inglés',
        'servicos_proximos' => 'Servicios Cercanos',
        'todos_servicos' => 'Todos los Servicios',
        'mapa' => 'Mapa Interactivo',
        'lista' => 'Lista Completa'
    ],
    'en' => [
        'titulo' => 'Mapping Public and Private Services',
        'slogan' => 'Find services near you',
        'bem_vindo' => 'Welcome',
        'buscar' => 'Search',
        'agendar' => 'Book',
        'agendado' => 'Booked',
        'cancelar' => 'Cancel Booking',
        'indisponivel' => 'Unavailable',
        'detalhes' => 'View details',
        'como_agendar' => 'How to book:',
        'horario' => 'Working Hours',
        'descricao' => 'Description',
        'rota' => 'Get Directions',
        'navegar' => 'Navigate',
        'compartilhar' => 'Share',
        'acessibilidade' => 'Accessibility',
        'contraste' => 'High Contrast',
        'modo_escuro' => 'Dark Mode',
        'tamanho_fonte' => 'Font Size',
        'portugues' => 'Portuguese',
        'espanhol' => 'Spanish',
        'ingles' => 'English',
        'servicos_proximos' => 'Nearby Services',
        'todos_servicos' => 'All Services',
        'mapa' => 'Interactive Map',
        'lista' => 'Complete List'
    ]
];

// Array de traduções atual
$t = $textos[$lang] ?? $textos['pt'];

// Configurações adicionais de localização
setlocale(LC_TIME, $lang . '_' . strtoupper($lang));
$domain = 'messages';
bindtextdomain($domain, __DIR__ . '/locale');
textdomain($domain);
?>
