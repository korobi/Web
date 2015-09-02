# Contribution Guidelines

#### For everyone:
- End files with a newline
- Use unix line-endings
- Reference issues where possible in commit messages
- Do **not** squash your changes, it makes it extremely hard to see what you’ve changed compared to the previous version of your pull request.
- Mark your PR as a work-in-progress by prefixing the title with `[WIP]`


#### For external contributors (users without push access to the repository):
- Create a fork of the project
- Make your changes on your fork. We recommend using a separate branch for each feature but this isn't too important
 - If you want to add a feature and the feature doesn't have a GitHub issue already, please drop by #korobi on EsperNet and discuss the idea with us or create your own issue
 - If you have any questions about making your change, don't hesitate to ask on IRC or GitHub
- Test your feature using the [supplied vagrant provisioning script](https://github.com/korobi/Web/wiki/Vagrant) or by [manually setting up the site](https://github.com/korobi/Web/wiki/Installation)
- Create a pull request to the master branch on Korobi/Web. We'll review the code and (usually) suggest changes.
- Make the requested changes (or further discuss them if they're unclear or you disagree)
- Once the PR looks good, we'll merge it and test it on staging for you. If all goes well, it'll be included in the next deploy to production.


#### For internal contributors (users with push access to the repository):
- Any changes should be done in its own branch (feature/log-caching, for example)
- Discuss changes on IRC if you get stuck or want feedback.
- When the feature is “ready” make a PR to merge feature/log-caching into master. 
 - If you make additional changes, push new commits to your branch.
- Mark your PR as `status: pending review` and ping some other team members to get it reviewed
- Make the requested changes to the PRs
- Ensure that the PR works as intended on staging and the tests still pass.
- When the feature has been checked and verified to be fixed/complete, PR to merge master into www1-stable if there isn't an existing PR. Be sure to specify if any new parameters are required to be filled when deploying.
