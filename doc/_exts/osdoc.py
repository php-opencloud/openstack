from docutils.parsers.rst import Directive
from docutils import nodes


class OsDoc(Directive):
    required_arguments = 1
    has_content = True

    def run(self):
        full_url = self.arguments[0]
        title = 'official documentation'

        text = []
        text.extend([
            nodes.Text('More information can be found in the '),
            nodes.reference(title, title, internal=False, refuri=self.arguments[0]),
            nodes.Text('.')
        ])

        return [nodes.paragraph(
            '',
            '',
            nodes.Text('More information can be found in the '),
            nodes.reference(title, title, internal=False, refuri=full_url),
            nodes.Text('.')
        )]


def setup(app):
    app.add_directive('osdoc', OsDoc)
    return {'version': '0.1'}
