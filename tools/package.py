"""Validate and package the WordPress deliverables without third-party dependencies."""
from pathlib import Path
import json,zipfile
root=Path(__file__).resolve().parents[1]
theme=root/'wp-content/themes/zalmi-shoes-child'
assert 'Template: astra' in (theme/'style.css').read_text()
count=0
for f in (theme/'elementor-templates').glob('*.json'):
 data=json.loads(f.read_text());assert data['version']=='0.4' and data['type']=='page'
 ids=set()
 def check(elements):
  for e in elements:
   assert e['id'] not in ids;ids.add(e['id'])
   assert e['elType'] in ('container','widget')
   if e['elType']=='widget':assert e['widgetType'] in ('heading','text-editor','button','image','shortcode')
   check(e['elements'])
 check(data['content']);count+=1
out=root/'dist';out.mkdir(exist_ok=True)
with zipfile.ZipFile(out/'zalmi-shoes-child.zip','w',zipfile.ZIP_DEFLATED) as z:
 for f in sorted(theme.rglob('*')):
  if f.is_file():z.write(f,Path(theme.name)/f.relative_to(theme))
with zipfile.ZipFile(out/'zalmi-shoes-child.zip') as z:
 assert z.testzip() is None
 assert 'zalmi-shoes-child/style.css' in z.namelist()
with zipfile.ZipFile(out/'zalmi-shoes-elementor-templates.zip','w',zipfile.ZIP_DEFLATED) as z:
 for f in sorted((theme/'elementor-templates').glob('*.json')):z.write(f,f.name)
print(f'Validated {count} template structures and both ZIP archives. Elementor runtime import is a separate required staging check.')
