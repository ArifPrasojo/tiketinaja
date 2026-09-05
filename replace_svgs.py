import os
import re

ICON_MAP = {
    'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293': 'check_circle',
    'M5 13l4 4L19 7': 'check',
    'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z': 'check_circle',
    
    'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293': 'cancel',
    'M6 18L18 6M6 6l12 12': 'close',
    'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3': 'warning',
    
    'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z': 'person',
    'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2': 'person',
    'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z': 'person',

    'M15 19l-7-7 7-7': 'chevron_left',
    'M9 5l7 7-7 7': 'chevron_right',
    'M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293': 'chevron_left',
    'M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707': 'chevron_right',
    'M19 9l-7 7-7-7': 'expand_more',
    
    'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z': 'search',
    'M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817': 'search',

    'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z': 'schedule',
    
    'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z': 'location_on',
    
    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5': 'stadium',
    
    'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z': 'confirmation_number',
    'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z': 'credit_card',
    'M15 11a3 3 0 11-6 0 3 3 0 016 0z': 'my_location',
    
    'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 110-1 .5.5 0 010 1zm.5 3.5a.5.5 0 11-1 0 .5.5 0 011 0z': 'qr_code_scanner',
    
    'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16': 'delete',
    
    'M12 4v16m8-8H4': 'add',
    'M12 6v6m0 0v6m0-6h6m-6 0H6': 'add',
    
    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2': 'calendar_month',
    
    'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z': 'image',
    
    'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z': 'call',
    
    'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z': 'lock',
    
    'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z': 'mail',
    
    'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z': 'progress_activity',
    
    'M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z': 'money',
    
    'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9': 'notifications',
    
    'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z': 'edit',
    
    'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1': 'logout',
    
    'M4 6h16M4 12h16M4 18h16': 'menu',
    
    'M15 12a3 3 0 11-6 0 3 3 0 016 0z': 'visibility',
    'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7': 'visibility',

    'M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z': 'location_on',
    
    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z': 'info'
}

SIZE_MAP = {}
for w in range(3, 13):
    SIZE_MAP[f'w-{w} h-{w}'] = f'text-[{w*4}px]'
    SIZE_MAP[f'h-{w} w-{w}'] = f'text-[{w*4}px]'
SIZE_MAP['w-3.5 h-3.5'] = 'text-[14px]'
SIZE_MAP['h-3.5 w-3.5'] = 'text-[14px]'

def map_size(cls):
    for k, v in SIZE_MAP.items():
        if k in cls:
            cls = cls.replace(k, v)
    
    cls_parts = cls.split()
    new_parts = []
    has_text_size = any(c.startswith('text-[') or c in ['text-sm','text-base','text-lg','text-xl','text-2xl'] for c in cls_parts)
    for p in cls_parts:
        if re.match(r'^(w|h)-[0-9]+(\.[0-9]+)?$', p):
            val = p.split('-')[1]
            try:
                px = int(float(val) * 4)
                if not has_text_size:
                    new_parts.append(f'text-[{px}px]')
                    has_text_size = True
            except:
                pass
        else:
            new_parts.append(p)
    return \" \".join(new_parts)

files_updated = set()

def process_file(filepath):
    if 'application-logo.blade.php' in filepath:
        return
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    def replace_svg(match):
        svg_full = match.group(0)
        svg_inner = match.group(3)
        
        if 'application-logo' in svg_full or 'opacity-[0.05]' in svg_full:
            return svg_full
            
        cls_match = re.search(r'class=\"([^\"]*)\"', match.group(1))
        cls = cls_match.group(1) if cls_match else ''
        
        cls = map_size(cls)
        
        icon_name = None
        for path_frag, name in ICON_MAP.items():
            if path_frag in svg_inner:
                icon_name = name
                break
                
        if not icon_name:
            if 'animate-spin' in cls:
                icon_name = 'progress_activity'
            elif 'M6 18L18 6M6 6l12 12' in svg_inner:
                icon_name = 'close'
            elif 'M15 19l-7-7 7-7' in svg_inner:
                icon_name = 'chevron_left'
            else:
                print(f\"No map found in {filepath}\")
                return svg_full
                
        cls = f\"material-symbols-outlined {cls}\".strip()
        cls = re.sub(r'\s+', ' ', cls)
        
        return f'<span class=\"{cls}\">{icon_name}</span>'

    new_content = re.sub(r'(<svg([^>]*)>)(.*?)(</svg>)', replace_svg, content, flags=re.DOTALL)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        files_updated.add(filepath)

for root, dirs, files in os.walk('resources/views'):
    for file in files:
        if file.endswith('.blade.php'):
            process_file(os.path.join(root, file))

print(\"Updated files:\")
for f in files_updated:
    print(f)
