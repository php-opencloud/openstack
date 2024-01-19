from docutils import nodes
from docutils.parsers.rst import directives
from sphinx.directives.code import LiteralInclude
import re

from sphinx.util.typing import OptionSpec


class Sample(LiteralInclude):
    option_spec: OptionSpec = {
        'full': directives.flag,
    }

    def run(self):
        self.arguments[0] = "/../samples/" + self.arguments[0]
        self.options['language'] = 'php'

        pattern = r"[\s+]?(\<\?php.*?]\);)"

        code_block = super(Sample, self).run()[0]
        if 'full' in self.options:
            return [code_block]

        string = str(code_block[0])

        match = re.match(pattern, string, re.S)
        if match is None:
            return [code_block]

        main_str = re.sub(pattern, "", string, 0, re.S).strip()
        if main_str == '':
            return [code_block]

        return [
            nodes.literal_block(main_str, main_str, language="php")
        ]


def setup(app):
    app.add_directive('sample', Sample)
    return {'version': '0.1'}
