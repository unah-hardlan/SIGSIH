<?php

return [

    

    'enabled' => env('DEBUGBAR_ENABLED', null),
    'hide_empty_tabs' => env('DEBUGBAR_HIDE_EMPTY_TABS', true), 
    'except' => [
        'telescope*',
        'horizon*',
    ],

    
    'storage' => [
        'enabled'    => env('DEBUGBAR_STORAGE_ENABLED', true),
        'open'       => env('DEBUGBAR_OPEN_STORAGE'), 
        'driver'     => env('DEBUGBAR_STORAGE_DRIVER', 'file'), 
        'path'       => env('DEBUGBAR_STORAGE_PATH', storage_path('debugbar')), 
        'connection' => env('DEBUGBAR_STORAGE_CONNECTION', null), 
        'provider'   => env('DEBUGBAR_STORAGE_PROVIDER', ''), 
        'hostname'   => env('DEBUGBAR_STORAGE_HOSTNAME', '127.0.0.1'), 
        'port'       => env('DEBUGBAR_STORAGE_PORT', 2304), 
    ],

    

    'editor' => env('DEBUGBAR_EDITOR') ?: env('IGNITION_EDITOR', 'phpstorm'),

    

    'remote_sites_path' => env('DEBUGBAR_REMOTE_SITES_PATH'),
    'local_sites_path' => env('DEBUGBAR_LOCAL_SITES_PATH', env('IGNITION_LOCAL_SITES_PATH')),

    

    'include_vendors' => env('DEBUGBAR_INCLUDE_VENDORS', true),

    

    'capture_ajax' => env('DEBUGBAR_CAPTURE_AJAX', true),
    'add_ajax_timing' => env('DEBUGBAR_ADD_AJAX_TIMING', false),
    'ajax_handler_auto_show' => env('DEBUGBAR_AJAX_HANDLER_AUTO_SHOW', true),
    'ajax_handler_enable_tab' => env('DEBUGBAR_AJAX_HANDLER_ENABLE_TAB', true),
    'defer_datasets' => env('DEBUGBAR_DEFER_DATASETS', false),
    
    'error_handler' => env('DEBUGBAR_ERROR_HANDLER', false),

    
    'clockwork' => env('DEBUGBAR_CLOCKWORK', false),

    

    'collectors' => [
        'phpinfo'         => env('DEBUGBAR_COLLECTORS_PHPINFO', false),         
        'messages'        => env('DEBUGBAR_COLLECTORS_MESSAGES', true),         
        'time'            => env('DEBUGBAR_COLLECTORS_TIME', true),             
        'memory'          => env('DEBUGBAR_COLLECTORS_MEMORY', true),           
        'exceptions'      => env('DEBUGBAR_COLLECTORS_EXCEPTIONS', true),       
        'log'             => env('DEBUGBAR_COLLECTORS_LOG', true),              
        'db'              => env('DEBUGBAR_COLLECTORS_DB', true),               
        'views'           => env('DEBUGBAR_COLLECTORS_VIEWS', true),            
        'route'           => env('DEBUGBAR_COLLECTORS_ROUTE', false),           
        'auth'            => env('DEBUGBAR_COLLECTORS_AUTH', false),            
        'gate'            => env('DEBUGBAR_COLLECTORS_GATE', true),             
        'session'         => env('DEBUGBAR_COLLECTORS_SESSION', false),         
        'symfony_request' => env('DEBUGBAR_COLLECTORS_SYMFONY_REQUEST', true),  
        'mail'            => env('DEBUGBAR_COLLECTORS_MAIL', true),             
        'laravel'         => env('DEBUGBAR_COLLECTORS_LARAVEL', true),          
        'events'          => env('DEBUGBAR_COLLECTORS_EVENTS', false),          
        'default_request' => env('DEBUGBAR_COLLECTORS_DEFAULT_REQUEST', false), 
        'logs'            => env('DEBUGBAR_COLLECTORS_LOGS', false),            
        'files'           => env('DEBUGBAR_COLLECTORS_FILES', false),           
        'config'          => env('DEBUGBAR_COLLECTORS_CONFIG', false),          
        'cache'           => env('DEBUGBAR_COLLECTORS_CACHE', false),           
        'models'          => env('DEBUGBAR_COLLECTORS_MODELS', true),           
        'livewire'        => env('DEBUGBAR_COLLECTORS_LIVEWIRE', true),         
        'jobs'            => env('DEBUGBAR_COLLECTORS_JOBS', false),            
        'pennant'         => env('DEBUGBAR_COLLECTORS_PENNANT', false),         
    ],

    

    'options' => [
        'time' => [
            'memory_usage' => env('DEBUGBAR_OPTIONS_TIME_MEMORY_USAGE', false), 
        ],
        'messages' => [
            'trace' => env('DEBUGBAR_OPTIONS_MESSAGES_TRACE', true),                  
            'capture_dumps' => env('DEBUGBAR_OPTIONS_MESSAGES_CAPTURE_DUMPS', false), 
        ],
        'memory' => [
            'reset_peak' => env('DEBUGBAR_OPTIONS_MEMORY_RESET_PEAK', false),       
            'with_baseline' => env('DEBUGBAR_OPTIONS_MEMORY_WITH_BASELINE', false), 
            'precision' => (int) env('DEBUGBAR_OPTIONS_MEMORY_PRECISION', 0),       
        ],
        'auth' => [
            'show_name' => env('DEBUGBAR_OPTIONS_AUTH_SHOW_NAME', true),     
            'show_guards' => env('DEBUGBAR_OPTIONS_AUTH_SHOW_GUARDS', true), 
        ],
        'gate' => [
            'trace' => false,      
        ],
        'db' => [
            'with_params'       => env('DEBUGBAR_OPTIONS_WITH_PARAMS', true),   
            'exclude_paths'     => [       
                
            ],
            'backtrace'         => env('DEBUGBAR_OPTIONS_DB_BACKTRACE', true),   
            'backtrace_exclude_paths' => [],   
            'timeline'          => env('DEBUGBAR_OPTIONS_DB_TIMELINE', false),  
            'duration_background'  => env('DEBUGBAR_OPTIONS_DB_DURATION_BACKGROUND', true),   
            'explain' => [                 
                'enabled' => env('DEBUGBAR_OPTIONS_DB_EXPLAIN_ENABLED', false),
            ],
            'hints'             => env('DEBUGBAR_OPTIONS_DB_HINTS', false),          
            'show_copy'         => env('DEBUGBAR_OPTIONS_DB_SHOW_COPY', true),       
            'slow_threshold'    => env('DEBUGBAR_OPTIONS_DB_SLOW_THRESHOLD', false), 
            'memory_usage'      => env('DEBUGBAR_OPTIONS_DB_MEMORY_USAGE', false),   
            'soft_limit'       => (int) env('DEBUGBAR_OPTIONS_DB_SOFT_LIMIT', 100),  
            'hard_limit'       => (int) env('DEBUGBAR_OPTIONS_DB_HARD_LIMIT', 500),  
        ],
        'mail' => [
            'timeline' => env('DEBUGBAR_OPTIONS_MAIL_TIMELINE', true),  
            'show_body' => env('DEBUGBAR_OPTIONS_MAIL_SHOW_BODY', true),
        ],
        'views' => [
            'timeline' => env('DEBUGBAR_OPTIONS_VIEWS_TIMELINE', true),                  
            'data' => env('DEBUGBAR_OPTIONS_VIEWS_DATA', false),                         
            'group' => (int) env('DEBUGBAR_OPTIONS_VIEWS_GROUP', 50),                    
            'inertia_pages' => env('DEBUGBAR_OPTIONS_VIEWS_INERTIA_PAGES', 'js/Pages'),  
            'exclude_paths' => [    
                'vendor/filament'   
            ],
        ],
        'route' => [
            'label' => env('DEBUGBAR_OPTIONS_ROUTE_LABEL', true),  
        ],
        'session' => [
            'hiddens' => [], 
        ],
        'symfony_request' => [
            'label' => env('DEBUGBAR_OPTIONS_SYMFONY_REQUEST_LABEL', true),  
            'hiddens' => [], 
        ],
        'events' => [
            'data' => env('DEBUGBAR_OPTIONS_EVENTS_DATA', false), 
            'excluded' => [], 
        ],
        'logs' => [
            'file' => env('DEBUGBAR_OPTIONS_LOGS_FILE', null),
        ],
        'cache' => [
            'values' => env('DEBUGBAR_OPTIONS_CACHE_VALUES', true), 
        ],
    ],

    

    'inject' => env('DEBUGBAR_INJECT', true),

    
    'route_prefix' => env('DEBUGBAR_ROUTE_PREFIX', '_debugbar'),

    
    'route_middleware' => [],

    
    'route_domain' => env('DEBUGBAR_ROUTE_DOMAIN', null),

    
    'theme' => env('DEBUGBAR_THEME', 'auto'),

    
    'debug_backtrace_limit' => (int) env('DEBUGBAR_DEBUG_BACKTRACE_LIMIT', 50),
];
