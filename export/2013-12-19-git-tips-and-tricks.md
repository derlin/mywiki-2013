---
title: "Git tips and tricks"
date: "2013-12-19"
---

## Replace the content of master by another branch (cleanly)
 
Sometimes, we worked a lot on a branch and, for an obscure reason, the default merge is not working as expected. One ugly way to proceed is to override the master branch by `git push origin local_branch:master -f`. Another approach, way cleaner, is to use the **ours** merge strategy:
 
```bash
> git checkout the_branch
> git merge -s ours master
> git checkout master
> git merge the_branch

> git commit -a
```
The resulting log message should be something like `Merge branch 'master' into the_branch`


<h2>branches tips and tricks</h2>

```bash
 # push a new branch to remote
 > git push origin local_branch:remote_branch

 # remove a branch from remote
 > git push origin :remote_branch

 # remove a local branch
 > git branch -d local_branch</pre>
```

## Notes about merge strategies
<table>
    <tr>
        <td>ours</td>
        <td>overrides the content ot the other branch by the current one, ignoring all changes from other branches (and their history). Useful when we want to totally replace the master by a side branch for example</td>
    </tr>
    <tr>
        <td>theirs</td>
        <td>exactly the opposite of ours. In both cases, one branch is totally superseded.</td>
    </tr>
    <tr>
        <td style="width:125px">recursive ours, recursive theirs</td>
        <td>forces conflicting hunks to be auto-resolved cleanly by favoring the current branch/the other branch.</td>
    </tr>
    <tr>
        <td>retursive patience</td>
        <td>With this option, merge-recursive spends a little extra time to avoid mismerges that sometimes occur due to unimportant matching lines (e.g., braces from distinct functions). Use this when the branches to be merged have diverged wildly (sic).</td>
    </tr>
</table>

**spaces**: to avoid conflict due to stupid spaces, use the options `ignore-all-space`, `ignore-space-change` or `ignore-space-at-eol` with the -X flag. Don't forget that they are only options, so a merge strategy should also be specified with the -s flag.

examples:
```bash
# select ours strategy
> git merge -s ours my_branch:master
# select recursive ours strategy
> git merge -s recursive -X ours branch1:branch2
# avoid conflicts due to whitespaces
> git merge -s recursive -X ignore-all-space master
```
[Complete list of options and explanations](https://www.kernel.org/pub/software/scm/git/docs/git-merge.html)

## Tagging

```bash
# create an annotated tag
git tag -a v1 -m "blabla" 

# show details about a tag
git show v1 
git show v1 -lw # more details
```

## Miscellaneous
```bash
# consult a file from a previous commit or another branch
> git show <head|branch>:file
# show a list of files modified in the given commit (here, HEAD)
> git diff-tree --no-commit-id --name-only -r HEAD

# merge a branch to the current one, but not the commits
# all the changes will be made locally (soo cool)
> git merge --squash <name of branch>

# stash and unstash
> git stash
> git stash apply

# with https: remember password:
> git config --global credential.helper 'cache --timeout 3600'
```


