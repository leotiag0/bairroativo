<?php
// Configuração básica de segurança e cabeçalhos
header('Content-Type: text/html; charset=utf-8');

// Idiomas suportados pelo sistema
$supported_langs = ['pt', 'es', 'en'];

// Determinar o idioma a ser usado (com fallback para português)
$lang = 'pt'; // Idioma padrão

// Verificar se foi solicitado um idioma específico
if (isset($_GET['lang'])) {
    $requested_lang = substr($_GET['lang'], 0, 2); // Pegar apenas os 2 primeiros caracteres
    if (in_array($requested_lang, $supported_langs)) {
        $lang = $requested_lang;
    }
}

// Definir cookie para lembrar a preferência de idioma (dura 30 dias)
setcookie('lang', $lang, time() + (86400 * 30), '/');

// Array completo de traduções para todos os idiomas suportados
$textos = [
    'pt' => [
        'titulo' => 'Mapeamento de Serviços Públicos e Privados',
        'slogan' => 'Encontre serviços próximos a você',
        'bem_vindo' => 'Bem-vindo',
        'descricao_sistema' => 'ao sistema de mapeamento de serviços',
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
        'lista' => 'Lista Completa',
        'ver_mapa' => 'Ver Mapa',
        'desc_mapa' => 'Explore serviços geolocalizados no mapa interativo',
        'ver_lista' => 'Ver Lista',
        'desc_lista' => 'Veja todos os serviços em formato de lista filtrável',
        'area_admin' => 'Área Administrativa',
        'desc_admin' => 'Acesse o painel de administração do sistema',
        'acessar' => 'Acessar',
        'acessar_mapa' => 'Acessar mapa de serviços',
        'acessar_lista' => 'Acessar lista de serviços',
        'acessar_admin' => 'Acessar área administrativa',
        'como_utilizar' => 'Como utilizar o sistema',
        'escolha_opcao' => 'Escolha a opção que melhor atende suas necessidades',
        'desc_servicos_proximos' => 'Encontre serviços próximos à sua localização atual',
        'detectar_localizacao' => 'Detectar minha localização',
        'erro_geolocalizacao' => 'Não foi possível determinar sua localização',
        'geolocalizacao_nao_suportada' => 'Geolocalização não suportada em seu navegador',
        'permissao_negada' => 'Permissão para acesso à localização foi negada',
        'localizacao_indisponivel' => 'Informações de localização indisponíveis',
        'tempo_esgotado' => 'Tempo de espera para obtenção da localização esgotado',
        'tentar_novamente' => 'Tentar novamente',
        'saiba_mais' => 'Saiba mais',
        'como_funciona_alt' => 'Ilustração mostrando como funciona o sistema',
        'meta_descricao' => 'Encontre serviços públicos e privados na cidade de São Paulo'
    ],
    'es' => [
        'titulo' => 'Mapeo de Servicios Públicos y Privados',
        'slogan' => 'Encuentra servicios cerca de ti',
        'bem_vindo' => 'Bienvenido',
        'descricao_sistema' => 'al sistema de mapeo de servicios',
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
        'lista' => 'Lista Completa',
        'ver_mapa' => 'Ver Mapa',
        'desc_mapa' => 'Explore servicios geolocalizados en el mapa interactivo',
        'ver_lista' => 'Ver Lista',
        'desc_lista' => 'Vea todos los servicios en formato de lista filtrable',
        'area_admin' => 'Área Administrativa',
        'desc_admin' => 'Acceda al panel de administración del sistema',
        'acessar' => 'Acceder',
        'acessar_mapa' => 'Acceder al mapa de servicios',
        'acessar_lista' => 'Acceder a la lista de servicios',
        'acessar_admin' => 'Acceder al área administrativa',
        'como_utilizar' => 'Cómo utilizar el sistema',
        'escolha_opcao' => 'Elija la opción que mejor se adapte a sus necesidades',
        'desc_servicos_proximos' => 'Encuentre servicios cercanos a su ubicación actual',
        'detectar_localizacao' => 'Detectar mi ubicación',
        'erro_geolocalizacao' => 'No se pudo determinar su ubicación',
        'geolocalizacao_nao_suportada' => 'Geolocalización no soportada en su navegador',
        'permissao_negada' => 'Permiso para acceso a la ubicación denegado',
        'localizacao_indisponivel' => 'Información de ubicación no disponible',
        'tempo_esgotado' => 'Tiempo de espera para obtener la ubicación agotado',
        'tentar_novamente' => 'Intentar nuevamente',
        'saiba_mais' => 'Saber más',
        'como_funciona_alt' => 'Ilustración mostrando cómo funciona el sistema',
        'meta_descricao' => 'Encuentre servicios públicos y privados en la ciudad de São Paulo'
    ],
    'en' => [
        'titulo' => 'Mapping Public and Private Services',
        'slogan' => 'Find services near you',
        'bem_vindo' => 'Welcome',
        'descricao_sistema' => 'to the service mapping system',
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
        'lista' => 'Complete List',
        'ver_mapa' => 'View Map',
        'desc_mapa' => 'Explore geolocated services on the interactive map',
        'ver_lista' => 'View List',
        'desc_lista' => 'See all services in a filterable list format',
        'area_admin' => 'Administrative Area',
        'desc_admin' => 'Access the system administration panel',
        'acessar' => 'Access',
        'acessar_mapa' => 'Access services map',
        'acessar_lista' => 'Access services list',
        'acessar_admin' => 'Access administrative area',
        'como_utilizar' => 'How to use the system',
        'escolha_opcao' => 'Choose the option that best suits your needs',
        'desc_servicos_proximos' => 'Find services near your current location',
        'detectar_localizacao' => 'Detect my location',
        'erro_geolocalizacao' => 'Could not determine your location',
        'geolocalizacao_nao_suportada' => 'Geolocation not supported in your browser',
        'permissao_negada' => 'Permission for location access denied',
        'localizacao_indisponivel' => 'Location information unavailable',
        'tempo_esgotado' => 'Location request timeout',
        'tentar_novamente' => 'Try again',
        'saiba_mais' => 'Learn more',
        'como_funciona_alt' => 'Illustration showing how the system works',
        'meta_descricao' => 'Find public and private services in São Paulo city'
    ]
];

// Definir as traduções para o idioma atual
$t = $textos[$lang] ?? $textos['pt'];

// Função auxiliar para tradução com fallback
function translate($key, $lang, $default = '') {
    global $textos;
    
    // Verificar se o texto existe no idioma solicitado
    if (isset($textos[$lang][$key])) {
        return $textos[$lang][$key];
    }
    
    // Fallback para português se disponível
    if ($lang !== 'pt' && isset($textos['pt'][$key])) {
        return $textos['pt'][$key];
    }
    
    // Fallback genérico
    return $default !== '' ? $default : $key;
}
?>
