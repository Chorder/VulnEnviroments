# VulnEnviroments 漏洞环境自动集成构建仓库

## 关于本仓库

本仓库不定期更新，包含常见的漏洞环境。
[漏洞环境列表](./EnviromentLists.md)

由Docker同步自动构建，Docker镜像地址位于：[https://hub.docker.com/r/chorder/vulns](https://hub.docker.com/r/chorder/vulns)

## 如何使用
如你所见，本仓库包含众多分支，每一个分支就是一个漏洞环境。漏洞环境分支以env开头，如env-DVWA，就是DVWA的Docker环境。

如果需要使用该分支的漏洞镜像，直接`docker pull chorder/vulns:分支名称`即可。

例如，获取DVWA漏洞镜像:

```
docker pull chorder/vulns:env-DVWA
```

## 如何参与贡献

你需要首先了解：
- Docker教程：[http://www.runoob.com/docker/docker-tutorial.html](http://www.runoob.com/docker/docker-tutorial.html)
- Git使用： [https://git-scm.com/book/zh/v1/](https://git-scm.com/book/zh/v1/)

首先fork本仓库并克隆，创建**以env-开头**的漏洞环境分支，例如env-CVE-XXXX-XXXX，以**分支根目录为镜像构建根目录**。

在准备好编译环境并**在本地编译测试通过**后，即可提交代码，并**在github中创建PR**，PR合并后即可自动化构建。。


```
git clone https://github.com/YOUR_NAME/VulnEnviroments
cd VulnEnviroments
git branch env-NEW_VULN_ENV_NAME
# do you job
# then push it
```

注：在构建漏洞镜像时，请尽量保持轻量化、代码简洁、结构合理的原则。


## 为什么要做这个仓库？

作为一个时常需要搭建漏洞环境的人，我深知大家都有这个需求。

有些仓库虽然只是搬运，但是能够有一个统一的地方来索引这些构建好的环境，总归是好的。

本着开放、分享、互助的精神，与其每个人都花费精力在这些简单却又重复的事情上，不如大家一起做一些让一起搬砖的兄弟们都能感受到生活美好的事情。

让漏洞环境一处搭建，多处受益。

---
Chorder
2019年4月26日
