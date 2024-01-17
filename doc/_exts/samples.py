from docutils import nodes
from sphinx.directives.code import LiteralInclude
from sphinx.util.nodes import make_refnode
from sphinx_toolbox.collapse import CollapseNode
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

        env = self.state.document.settings.env
        ref_node = make_refnode(
            env.app.builder,
            fromdocname=env.docname,
            todocname='use',
            targetid='',
            child=nodes.Text('Use OpenStack library')
        )

        return [
            CollapseNode(
                '',
                'show full code',
                nodes.paragraph(
                    '',
                    '',
                    nodes.Text('Example of how to create OpenStack object. See '),
                    ref_node,
                    nodes.Text(' for all options.')
                ),
                nodes.literal_block(auth_str, auth_str, language="php")
            ),
            nodes.literal_block(main_str, main_str, language="php")
        ]


def setup(app):
    app.add_directive('sample', Sample)
    return {'version': '0.1'}
