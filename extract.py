import re
import os

markdown_path = 'SupportTicketSystem.md'
with open(markdown_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Pattern matches: **`path/to/file`**\n```<language>\n<code>\n```
pattern = re.compile(r'\*\*`([^`]+)`\*\*\s*```\w+\n(.*?)```', re.DOTALL)

matches = pattern.findall(content)
for filepath, code in matches:
    print(f"Extracting: {filepath}")
    
    dir_name = os.path.dirname(filepath)
    if dir_name:
        os.makedirs(dir_name, exist_ok=True)
    
    with open(filepath, 'w', encoding='utf-8') as out_f:
        if filepath.endswith('.php') and not code.lstrip().startswith('<?php'):
            out_f.write('<?php\n\n')
        out_f.write(code.strip() + '\n')

print(f"Successfully extracted {len(matches)} files.")
