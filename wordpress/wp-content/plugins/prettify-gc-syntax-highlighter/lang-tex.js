// PR_(STRING|KEYWORD|COMMENT|TYPE|LITERAL|PUNCTUATION|PLAIN|TAG|DECLARATION|SOURCE|ATTRIB_(NAME|VALUE)
PR.registerLangHandler(
  PR.createSimpleLexer(
    [
      ['opn', /^(?=[^\\])(?:\{|\\\[)/, null, '{['],
      ['clo', /^(?=[^\\])(?:\}|\\\])/, null, '}]']
    ],
    [
      [PR.PR_TAG, /^\\(?:text|math)?(?:rm|it|sl|tt|sc)\b/],
      [PR.PR_KEYWORD, /^\\(?:[^\\])\w*\b/],
      [PR.PR_COMMENT, /^(?=[^\\])%[^\r\n]*/],
      [PR.PR_PUNCTUATION, /^(?:\\\\|\\;|\\,|\\!|~|\ |\^)/],
      [PR.PR_PUNCTUATION, /^(?=[^\\])(?:\$){1,2}/],
      [PR.PR_PUNCTUATION, /^(?:\\\[|\\\])/],
      [PR.PR_LITERAL,
/^(?:by|at|to|spread)? ?(?:-)?(?:\d+)?\.?\d+ ?(?:true)? ?(?:pt|pc|in|bp|cm|mm|dd|cc|em|ex|mu|\\fil)\b/],
      [PR.PR_LITERAL, /^(?=[^\\])#\d?/],
      [PR.PR_LITERAL, /^(?=[^\\])[&_]/]
    ]),
  ['tex']);
