from docutils import nodes
from sphinx.addnodes import download_reference
from sphinx.directives.code import LiteralInclude
import re

class Sample(LiteralInclude):

  def run(self):
    self.arguments[0] = "/../samples/" + self.arguments[0]
    self.options['language'] = 'php'

    pattern = r"[\s+]?(\<\?php.*?]\);)"

    code_block = super(Sample, self).run()[0]
    string = str(code_block[0])

    match = re.match(pattern, string, re.S)
    if match is None:
      return [code_block]

    auth_str = match.group(1).strip()
    main_str = re.sub(pattern, "", string, 0, re.S).strip()

    show_hide_btn = download_reference(reftarget=self.arguments[0])

    return [
        show_hide_btn, 
        nodes.literal_block(auth_str, auth_str, language="php"), 
        nodes.literal_block(main_str, main_str, language="php")]

def setup(app):
  app.add_directive('sample', Sample)
  return {'version': '0.1'}
