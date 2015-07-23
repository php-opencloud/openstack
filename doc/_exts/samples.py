from sphinx.directives import LiteralInclude
from docutils import nodes
from sphinx.addnodes import download_reference
from sphinx.writers.html import HTMLTranslator

class Sample(LiteralInclude):

  def run(self):
    self.arguments[0] = "/../samples/" + self.arguments[0]
    self.options['language'] = 'php'
    parent_node = super(Sample, self).run()[0]

    download = download_reference(reftarget=self.arguments[0])
    return [download, parent_node]

def visit_download_reference(self, node):
  self.context.append('<a href="javascript:void(0);" class="toggle btn">Show/hide code sample</a>')

def setup(app):
  app.add_node(download_reference, html=(visit_download_reference, HTMLTranslator.depart_download_reference))
  app.add_directive('sample', Sample)
  return {'version': '0.1'}