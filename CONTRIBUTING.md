# Contributing to FeatureBundle

Hey, thank you for your interest in this bundle! Here at [AdEspresso](https://adespresso.com/) we try to improve this bundle in many ways, when possible and when we need it. Still, every external feedback and proposal is always well accepted! It's open-source, after all...

However, be sure to take a look to our guidelines before you start. It's not about boring you, but we have some conventions to keep everything clean, and we would like to preserve them along our way. 

## Coding Conventions

* Your code must follow the [Symfony Code Standards](http://symfony.com/doc/current/contributing/code/standards.html);
* You should use [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) for a better code quality;
* If you are introducing something new, write new tests. If you are modifying something, don't forget to edit tests accordingly. In any case, the word is always the same: **tests**;

## Reporting Guidelines

* If you want to report a bug, or suggest something new, open an issue;
* If you are reporting a bug, try to **provide all the needed steps to reproduce the issue**;

## Git/GitHub Conventions

* Name your branch with dashes, not underscores;
* The first letter of a commit message is uppercase;
* Do not mix many changes in a single commit. Do many of them if needed;
* If you are fixing a bug, the related branch name should start with "fix-";
* Use imperative present tense for git commit messages, not past tense. We like this way;
* Name your branch with a meaningful name. For instance, "add-phpunit-as-dependency" is good, "branch-for-issue-1234" is not; 
* Here on GitHub we use Scrutinizr to scan code and Travis to run tests for better results. Every time you send a pull request, your code (and your tests) will be analyzed (and executed). As you probably imagine, **if you break tests or something goes wrong we can't merge your code**. So, double check your code and use the flags here on GitHub to keep an eye on the situation.