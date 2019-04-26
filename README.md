# VulnEnviroments 漏洞环境自动集成构建仓库

## 关于本仓库

本仓库不定期更新，包含常见的漏洞环境。

本仓库由Docker同步自动构建，Docker镜像地址位于：[https://cloud.docker.com/repository/docker/chorder/vulns](https://cloud.docker.com/repository/docker/chorder/vulns)

## 如何使用

你需要首先掌握：
- Docker教程：[http://www.runoob.com/docker/docker-tutorial.html](http://www.runoob.com/docker/docker-tutorial.html)
- Git使用： [https://git-scm.com/book/zh/v1/](https://git-scm.com/book/zh/v1/)

如你所见，本仓库包含众多分支，其中漏洞环境分支以env开头，如env-DVWA，就是一个构建好的DVWA Docker环境。

每个分支中都包含一个独立的Docker构建环境，需要查看哪个分支的源码，就用`git checkout 分支名称`切换到该分支即可。

如果需要使用从该分支构建好的镜像，使用`docker pull chorder/vulns:分支名称`即可

以DVWA漏洞环境为例：

```bash
root@aliyun-deb:~# git clone https://github.com/Chorder/VulnEnviroments.git
Cloning into 'VulnEnviroments'...
remote: Enumerating objects: 28, done.
remote: Counting objects: 100% (28/28), done.
remote: Compressing objects: 100% (19/19), done.
remote: Total 28 (delta 9), reused 25 (delta 6), pack-reused 0
Unpacking objects: 100% (28/28), done.
root@aliyun-deb:~# cd VulnEnviroments/
root@aliyun-deb:~/VulnEnviroments# git branch -r
  origin/HEAD -> origin/master
  origin/env-DVWA
  origin/master
root@aliyun-deb:~/VulnEnviroments# git checkout env-DVWA 
Branch env-DVWA set up to track remote branch env-DVWA from origin.
Switched to a new branch 'env-DVWA'
root@aliyun-deb:~/VulnEnviroments# ls
Dockerfile  dvwa.conf  DVWA-master.zip  mysql_init.sh  README.md  sources.list  start.sh
root@aliyun-deb:~/VulnEnviroments# docker pull chorder/vulns:env-DVWA
env-DVWA: Pulling from chorder/vulns
e79bb959ec00: Pull complete 
1c19439741a1: Pull complete 
9d0b9970aba5: Pull complete 
236a2aa66421: Pull complete 
2541b2af94cd: Pull complete 
052aed59bbfb: Pull complete 
e6c58a89eb07: Pull complete 
0da5bfe70119: Pull complete 
46f57ef72ad0: Pull complete 
1445944f09ce: Pull complete 
b850cc06de67: Pull complete 
ecaaadbcce9b: Pull complete 
0c54343d2152: Pull complete 
bba4baa45b56: Pull complete 
24ce9ad29bf7: Pull complete 
Digest: sha256:e21558a28b223148eb3900135fdb2bb225f8084f961d8d0688f36fae1d310c12
Status: Downloaded newer image for chorder/vulns:env-DVWA
root@aliyun-deb:~/VulnEnviroments# 
```


## 如何贡献 (估计没人)

首先fork本仓库并克隆你自己fork过去的仓库，在仓库中创建新的分支。漏洞环境请以env-开头，例如**env-CVE-XXXX-XXXX**

在本地构建好Docker工程并测试通过以后，push到github并创建PR即可。

PR审核通过后即可自动化构建。


## 为什么要做这个仓库？

作为一个时常需要搭建漏洞环境的人，我深知大家都有这个需求。本着开放、分享、互助的精神，与其每个人都花费精力在这些简单却又重复的事情上，
不如大家一起做一些让同在安全一线搬砖的兄弟们都能感受到生活美好的事情。让漏洞环境一处搭建，多处受益。

---
Chorder
2019年4月26日







