

## 接口名称1 用户登录332

### 1) 请求地址

>https://ccc.cashgox.top/Index/ajaxSignIn

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数62:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|username|用户名|string|Y|测试用户9680304707|
|password|密码|string|Y|测试密码 随机123456|



### 5) 请求返回结果3:

```
{
    "status": 200,
    "message": "login successful",
    "data": "/Repay/index.shtml"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|状态码|string|Y|200为正确,其他都是错误|
|message|提示|string|Y|-|
|data|数据|string|Y|-|




## 接口名称
用户退出
### 1) 请求地址

>https://ccc.cashgox.top/Index/ajaxLogOut

### 2) 调用方式：HTTP get

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:



### 5) 请求返回结果:

```
{
    "status": 200,
    "message": "Logout successful"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|状态码|string|Y|200为正确退出|
|message||string|Y|-|

## 接口名称
当前借款订单

### 1) 请求地址

>https://ccc.cashgox.top/Repay/loans

### 2) 调用方式：HTTP get

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:



### 5) 请求返回结果:

```
{
    "status": 200,
    "data": {
        "status": -100,
        "quota": "5000",
        "default_loan_time": 7,
        "default_deadline": [
            "7",
            "10",
            "15",
            "20"
        ],
        "default_loan_interest": "0.043",
        "default_repay_interest": "0.0072"
    },
    "message": "If the user is not logged in, the default quota will be displayed"
}
```


### 6) 请求返回结果参数说明:

####未登录时

|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|响应状态|string|Y|200为正确响应|
|data||string|Y|-|
|. status|借款状态|integer|Y|-100为用户未登录,0为未还款,1逾期，2.已还款,4,续期|
|. quota|默认额度|string|Y|默认|
|. default_loan_time|默认借款时间|string|Y|-|
|. default_deadline|默认借款周期|string|Y|-|
|. default_loan_interest|默认借款费率|string|Y|-|
|. default_repay_interest|默认还款费率|string|Y|-|
|message||string|Y|-|


####已登录
```
{
    "status": 200,
    "data": {
        "money": 5000,
        "repay_money": 5200,
        "addtime": "1665209765",
        "repayment_time": 1665187200,
        "loan_time": "7",
        "billId": "37050",
        "confirm": 0,
        "serviceFee": 1565.2,
        "bill": {
            "id": "37050",
            "toid": "90870",
            "oid": "2022100806160549047940",
            "delay": "0",
            "delay_day": "0",
            "repay_interest": "252.00",
            "repayment_time": "1665187200",
            "add_time": "1665209765",
            "money": "5000.00"
        },
        "hasOverdue": true,
        "doquota": "0.00",
        "commission": 0,
        "quota": "5000.00",
        "default_loan_time": 7,
        "default_deadline": [
            "7",
            "10",
            "15",
            "20"
        ],
        "default_loan_interest": "0.043",
        "default_repay_interest": "0.0072",
        "next_loan": "20000",
        "approval_rate": "0.96",
        "next_loan_period": "16",
        "status": 1
    },
    "message": "Successfully"
}
```


|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|响应状态码|string|Y|200代表Ok|
|data||string|Y|-|
|money|贷款金额|string|Y|-|
|status|贷款状态|string|Y|-100为用户未登录,0为未还款,1逾期，2.已还款,4,续期|
|repay_money|到期应还金额|float|Y|-|
|addtime|贷款开始时间|string|Y|-|
|repayment_time|最后还款时间|string|Y|-|
|loan_time|贷款期限|string|Y|-|
|billId|账单id|string|Y|-|
|confirm|是否确认|string|Y|-|
|serviceFee|服务费用|string|Y|-|
|bill|账单,参考下面|json object|Y|-|
|hasOverdue|是否逾期|string|Y|-|
|nextRepayDay|下一次还款时间|string|Y|-|
|repayment_time|最后还款时间|string|Y|-|
|delayFee|延期费用|string|Y|-|
|doquota|剩余额度|string|Y|-|
|commission|佣金|string|Y|-|
|quota|总额度|string|Y|-|
|default_loan_time|默认贷款期限|string|Y|-|
|default_deadline|贷款周期|string|Y|-|
|default_loan_interest|贷款费率|string|Y|-|
|default_repay_interest|还款费率|string|Y|-|
|next_loan|下一个可用贷款金额|string|Y|-|
|approval_rate|批准率|string|Y|-|
|next_loan_period|下一个贷款期限|string|Y|-|
|message||string|Y|-|

 
 >>bill 数据内容

 |字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|id|账单id|string|Y|-|
|toid|关联借款ID|string|Y|-|
|oid|关联借款订单号|string|Y|-|
|delay|是否延期|string|Y|-|
|delay_day|延期天数|string|Y|-|
|repay_interest|延期费率|string|Y|-|
|add_time|贷款开始时间|string|Y|-|




## 接口名称
借款账单
### 1) 请求地址

>https://ccc.cashgox.top/Repay/orders

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|username||string|Y|-|
|password||string|Y|-|



### 5) 请求返回结果:

```
{
    "status": 200,
    "data": {
        "orders": [
            {
                "id": "90870",
                "oid": "2022100806160549047940",
                "time": "7",
                "interest": "1504.00",
                "reply_rate": "0.00720000",
                "start_time": "1664582400",
                "addtime": "1665209765",
                "money": "5000",
                "delay": "0",
                "status": 1,
                "repay_interest": 252,
                "repay_money": 5452,
                "repayment_time": 1665187200,
                "overFee": 200,
                "next_loan": "20000",
                "approval_rate": "0.96",
                "next_loan_period": "16"
            }
        ]
    },
    "message": "Successfully"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|响应状态码|string|Y|200代表成功|
|data||string|Y|-|
|orders||string|Y|-|
|id|账单id|string|Y|-|
|oid|账单关联订单ID|string|Y|-|
|time|借款时间|string|Y|-|
|interest|借款费用|string|Y|-|
|reply_rate|还款费率|string|Y|-|
|start_time|借款开始时间|string|Y|-|
|addtime|添加时间|string|Y|-|
|money|借款金额|string|Y|-|
|delay|是否延期|string|Y|-|
|repay_interest|还款费用|string|Y|-|
|repay_money|应还金额|string|Y|-|
|repayment_time|还款时间|string|Y|-|
|overFee|逾期费用|string|Y|-|
|next_loan|下一个可用贷款金额|string|Y|-|
|approval_rate|批准率|string|Y|-|
|next_loan_period|下一个贷款期限|string|Y|-|
|message||string|Y|-|




## 接口名称
贷款延期
### 1) 请求地址

>https://ccc.cashgox.top/Repay/ajaxDelay

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|oid|账单id|string|Y|cashgo当前借款订单中返回的账单id(billId)|



### 5) 请求返回结果:

```
{
    "status": 200,
    "data": "",
    "message": "Deferred success"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status|响应状态码|string|Y|200代表Ok|
|data||string|Y|-|
|message||string|Y|-|



## 接口名称
再次申请贷款
### 1) 请求地址

>https://ccc.cashgox.top/Loan/reLoan

### 2) 调用方式：HTTP post/get

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:




### 5) 请求返回结果:

```
{
    "status": 200,
    "message": "Successful",
    "data": ""
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status||string|Y|200复借成功，其他为失败|
|message||string|Y|-|
|data||string|Y|-|




## 接口名称
ajax上传图片
### 1) 请求地址

>https://ccc.cashgox.top/Info/ajaxUpload

### 2) 调用方式：HTTP post form-data

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|fileName|上传文件名|string|Y|值和后面一个参数名保持一致|
|fileName的值|文件内容|file|Y|文件|


<pre>
POST https://ccc.cashgox.top/Info/ajaxUpload
Cookie: PHPSESSID=h76e188lbq6tbkk7f7m3a2tg97
Content-Type: multipart/form-data; boundary=WebAppBoundary

--WebAppBoundary
Content-Disposition: form-data; name="fileName"

file
--WebAppBoundary
Content-Disposition: form-data; name="file"; filename="a.jpg"

< 文件.jpg
--WebAppBoundary--
</pre>

### 5) 请求返回结果:

```
{
  "status": 200,
  "message": "SUCCEED",
  "data": "http:\/\/{域名}\/Public\/Upload\/20221010\/d78293370776098550cf050d59c2a2fb.jpg"
}
```




## 接口名称
提交feedback反馈
### 1) 请求地址

>https://ccc.cashgox.top/Feedback/ajaxCreate

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|comments|内容|string|Y|必须|
|attachments|文件或者图片|string|N|-|
|contact|联系方式|string|Y|电话或者邮箱|



### 5) 请求返回结果:

```
{
    "status": 200,
    "data": "",
    "message": "Submit Successfully"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status||string|Y|200成功|
|data||string|Y|-|
|message||string|Y|-|



## 接口名称
贷款确认
### 1) 请求地址

>https://ccc.cashgox.top/Index/ajaxApply

### 2) 调用方式：HTTP get

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


### 5) 请求返回结果:

```
{
    "status": 200,
    "message": "apply successful"
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status||string|Y|200代表成功|
|message||string|Y|-|





## 接口名称
批量添加通讯录
### 1) 请求地址

>https://ccc.cashgox.top/Contact/ajaxPush

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:


#### POST参数:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|contacts||oject array|Y| 字段|
|name|联系人姓名|string|Y| zhangsan|
|phone|联系人电话|string|Y| 123132131|
|relate|关系|string|N| 同事,兄弟，父母|

数据格式
<pre>
POST http://local.cash2/Contact/ajaxPush
Content-Type: application/x-www-form-urlencoded; charset=utf-8
Cookie: PHPSESSID=h76e188lbq6tbkk7f7m3a2tg97

contacts[0][name]=zhangsan&contacts[0][phone]=123455745&contacts[0][relate]=父母
</pre>

### 5) 请求返回结果:

```
{
    "status": 200,
    "message": "Successful",
    "data": ""
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|status||string|Y|200为添加成功|
|message||string|Y|-|
|data||string|Y|-|

## 接口名称
批量添加通讯录
### 1) 请求地址

>https://ccc.cashgox.top/Index/contact

### 2) 调用方式：HTTP post|get

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:
无

数据格式
<pre>
POST http://local.cash2/Contact/ajaxPush
Content-Type: application/x-www-form-urlencoded; charset=utf-8
Cookie: PHPSESSID=h76e188lbq6tbkk7f7m3a2tg97

contacts[0][name]=zhangsan&contacts[0][phone]=123455745&contacts[0][relate]=父母
</pre>

### 5) 请求返回结果:

```
{
    "status": 200,
    "message": "successful",
    "data": {
        "phone_number": "1231312312",
        "whatapp": "aaaaaaaaaassfa"
    }
}
```


### 6) 请求返回结果参数说明:
|字段名称       |字段说明         |   类型    |     备注 |
| -------------|:--------------:|:-------:|-------:|
|phone_number||   手机号   | string |    |
|whatapp|| whatapp | string |   - |


## 接口名称
图片上传
### 1) 请求地址

>域名/Info/ajaxUpload

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:
|字段名称       |     字段说明     |类型            |必填            |        备注 |
| -------------|:------------:|:--------------:|:--------------:|----------:|
|file|    二进制文件     |oject array|Y|        字段 |
|fileName| 文件名,传固定值file |string|Y|      file |

### 5) 请求返回结果:

```
{
    "info": "20240302/0346d3c44c8fd65464661bf53cef9bc4.png",
    "status": 1,
    "url": ""
}
```


### 6) 请求返回结果参数说明:
| 字段名称    |字段说明         |                                  类型                                  |     备注 |
|---------|:--------------:|:--------------------------------------------------------------------:|-------:|
| info    || 文件路径,此处获取到的路径需要加上域名/Public/Upload/,最终的图片路径是：域名/Public/Upload/+info内容 | string |    |


## 接口名称
借款详情/延期还款详情
### 1) 请求地址

>域名/Repay/detail

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:
| 字段名称 |           字段说明           |   类型   | 必填 |        备注 |
|------|:------------------------:|:------:|:--:|----------:|
| oid  |           订单id           | string | Y  |        字段 |
| next | 不填则为借款订单详情页,传delay则为订单详情 | string | N  |       |

### 5) 请求返回结果:

```
{
    "status": 200,
    "data": {
        "oid": "2024020110593245854970",
        "needRepayMoney": 5060,
        "needRepaymentTime": 1706716800,
        "needRepaymentDay": "01-02-2024",
        "bill": {
            "id": "9564",
            "uid": "9569",
            "toid": "9605",
            "oid": "2024020110593245854970",
            "billnum": "1",
            "money": "2020.00",
            "interest": "848.00",
            "overdue": "3030.00",
            "delay": "0",
            "delay_day": "0",
            "delay_rate": "0.00000000",
            "delay_num": "0",
            "delay_unpaid": "0.00",
            "repay_interest": "100.00",
            "repaid_money": "0.00",
            "repayment_time": "1706716800",
            "utr": null,
            "utr_image": null,
            "repay_image": null,
            "status": "1",
            "add_time": "1706112000",
            "repay_time": "0",
            "overdue_settime": "1709310512",
            "overdue_smsstatus": "0",
            "overdue_xq": null
        },
        "delayFee": 848.4,
        "nextRepayDatetime": 1707321600,
        "nextRepayDay": "08-02-2024"
    },
    "message": "Successfully"
}
```


### 6) 请求返回结果参数说明:
| 字段名称    |   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| needRepayMoney    |  需要还款金额   |   | string |    |
| needRepaymentTime    |  下一次还款时间  |   | string |    |
| needRepaymentDay    |  最后还款日期   |   | string |    |
| delayFee    |   延期费用    |   | string |    |
| nextRepayDatetime    | 延期还款之后的时间 |   | string |    |
| nextRepayDay    | 延期还款之后的日期 |   | string |    |
| nextRepayDay    | 延期还款之后的日期 |   | string |    |
| money    |   借款金额    |   | double |    |
| interest    |    费用     |   | double |    |


## 接口名称
上传凭证
### 1) 请求地址

>域名/Repay/repayProof

### 2) 调用方式：HTTP post

### 3) 接口描述：

* 接口描述详情

### 4) 请求参数:
| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| oid | 订单id  | string | Y  |        字段 |
| utr | utr单号 | string | N  |       |
| utr_image | utr图片 | string | N  |       |
| repay_image | 付款图片  | string | N  |       |

### 5) 请求返回结果:

```
{"info":"Successful","status":1,"url":""}
```


### 6) 请求返回结果参数说明:
| 字段名称    |   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|


## 接口名称
还款展示页面(原H5还款显示页面)
### 1) 请求地址

>域名/Repay/loanDetail

### 2) 调用方式：HTTP post

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:
| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| oid | 订单id  | string | Y  |        字段 |
```
{
    "status": 200,
    "data": {
        "bill": {
            "id": "247598",
            "uid": "247475",
            "toid": "247678",
            "oid": "2023051914035297833260",
            "billnum": "1",
            "money": "1955000.00",
            "interest": "821100.00",
            "overdue": "44378500.00",
            "delay": "0",
            "delay_day": "0",
            "delay_rate": "0.00000000",
            "delay_num": "0",
            "delay_unpaid": "0.00",
            "repay_interest": "100.00",
            "repaid_money": "0.00",
            "repayment_time": "1684425600",
            "utr": null,
            "utr_image": null,
            "repay_image": null,
            "status": "1",
            "add_time": "1683820800",
            "repay_time": "0",
            "overdue_settime": "1723701716",
            "overdue_smsstatus": "0",
            "overdue_xq": null
        },
        "oid": "2023051914035297833260",
        "needRepayMoney": 46333600,
        "needRepaymentTime": 1684425600,
        "needRepaymentDay": "19-05-2023"
    },
    "message": "Successfully"
}
```


### 6) 请求返回结果参数说明:
| 字段名称    |   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| oid    |  订单id   |   | string |    |
| needRepayMoney    |  需要还款金额  |   | string |    |
| needRepaymentTime    |  截止还款时间戳   |   | string |    |
| needRepaymentDay    |  截止还款时间   |   | string |    |



## 接口名称
延期还款展示页面(原H5延期还款显示页面)
### 1) 请求地址

>域名/Repay/loanDelay

### 2) 调用方式：HTTP post

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:
| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| oid | 订单id  | string | Y  |        字段 |
```
{
    "status": 200,
    "data": {
        "bill": {
            "id": "247598",
            "uid": "247475",
            "toid": "247678",
            "oid": "2023051914035297833260",
            "billnum": "1",
            "money": "1955000.00",
            "interest": "821100.00",
            "overdue": "44378500.00",
            "delay": "0",
            "delay_day": "0",
            "delay_rate": "0.00000000",
            "delay_num": "0",
            "delay_unpaid": "0.00",
            "repay_interest": "100.00",
            "repaid_money": "0.00",
            "repayment_time": "1684425600",
            "utr": null,
            "utr_image": null,
            "repay_image": null,
            "status": "1",
            "add_time": "1683820800",
            "repay_time": "0",
            "overdue_settime": "1723701716",
            "overdue_smsstatus": "0",
            "overdue_xq": null
        },
        "oid": "2023051914035297833260",
        "needRepayMoney": 45649350,
        "needRepaymentTime": 1684425600,
        "needRepaymentDay": "19-05-2023",
        "delayFee": 821100,
        "nextRepayDatetime": 1685030400,
        "overdueFee": 44378500,
        "nextRepayDay": "26-05-2023"
    },
    "message": "Successfully"
}
```

### 6) 请求返回结果参数说明:
| 字段名称    |   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| oid    |  订单id   |   | string |    |
| needRepayMoney    |  需要还款金额  |   | string |    |
| needRepaymentTime    |  截止还款时间戳   |   | string |    |
| needRepaymentDay    |  截止还款时间   |   | string |    |
| delayFee    |  延期还款费用   |   | string |    |
| nextRepayDatetime    |  延期后下一次还款时间戳   |   | string |    |
| overdueFee    |  逾期费用   |   | string |    |
| nextRepayDay    |  延期后下一次还款时间   |   | string |    |


## 接口名称
语言列表和默认语言
### 1) 请求地址

>域名/Index/languages

### 2) 调用方式：HTTP post/get

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:

| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|

```
{
    "status": 200,
    "languages": [
        "Spanish",
        "English"
    ],
    "default": "English",
    "target": "web"
}
```

### 6) 请求返回结果参数说明:

|字段名称|   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| languages    |  语言列表   | array | | |    |
| default    |  默认语言  | string|  |  |    |
| target    |  跳转目标  | string|  web跳转到h5,app 内部跳转 |  |   |


## 接口名称
语言列表和默认语言
### 1) 请求地址

>域名/Repay/payData

### 2) 调用方式：HTTP post

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:

| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| oid | 订单id  | string | Y  |        字段 |
```
{
    "status": 200,
    "data":"http://localhost"
}
```

### 6) 请求返回结果参数说明:

|字段名称|   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| data    |  支付链接   | array |拿到此url在下一个接口使用 | |    |


## 接口名称
语言列表和默认语言
### 1) 请求地址

>上一个接口的url

### 2) 调用方式：HTTP post

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:

| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| paytype | 支付方式  | string | Y  |        1为 pst，2为paycash |
```
{
    "code": 200,
    "msg": "successful",
    "data": {
        "reference": "472445424642024082500100005438",
        "barcode": "PB1825066649678647296",
        "expire": "2024-08-19 15:06:09",
        "path": "http://local.gfpay/assets/tmp/32.png",
        "origin_img": "https://sipelatam.s3.us-west-2.amazonaws.com/prod/referencia/barCode/20240818/839cc2e12af54993964fde14296c7a14.png",
        "money": "100.00",
        "name": "stp",
        "image": "http://local.gfpay/assets/front/images/pst.png"
    }
}

//在线支付返回结果
{
    "status": 200,
    "data": {
        "code": 200,
        "msg": "successful",
        "data": {
            "reference": "706180301567011837",
            "barcode": "GV1825068937331875840",
            "expire": "2024-08-19 15:15:37",
            "checkout": "https://checkout-mx.eastpay.top?checkout=MjAyNDA4MTgxNTExNTcyNzUwNCZHVjE4MjUwNjg5MzczMzE4NzU4NDAmNzA2MTgwMzAxNTY3MDExODM3",
            "money": "100.00",
            "path": "http://local.gfpay/assets/tmp/37.png",
            "name": "Paycash",
            "image": "http://local.gfpay/assets/front/images/pay_cash.png"
        }
    },
    "message": "successful"
}
```

### 6) 请求返回结果参数说明:

|字段名称|   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| reference    |  reference   | string | | |    |
| barcode    |  barcode   | string | | |    |
| expire    |  过期时间   | string | | |    |
| money    |  支付金额   | string | | |    |
| path    |  条形码   | string | | |    |
| name    |  支付方式名称   | string | | |    |
| image    |  支付方式logo  | string | | |    |
| checkout    |  在线支付收银台地址  | string | | |    |


## 接口名称
请求支付信息
### 1) 请求地址

>/Repay/payTransf

### 2) 调用方式：HTTP post

### 3) 接口描述：
* 接口描述详情

### 4) 请求参数:

| 字段名称 | 字段说明  |   类型   | 必填 |        备注 |
|---|:-----:|:------:|:--:|----------:|
| paytype | 支付方式  | string | Y  |        1为 pst，2为paycash |
| url | 支付链接  | string | Y  |        上上个接口返回的支付链接 |
```
{
    "code": 200,
    "msg": "successful",
    "data": {
        "reference": "472445424642024082500100005438",
        "barcode": "PB1825066649678647296",
        "expire": "2024-08-19 15:06:09",
        "path": "http://local.gfpay/assets/tmp/32.png",
        "origin_img": "https://sipelatam.s3.us-west-2.amazonaws.com/prod/referencia/barCode/20240818/839cc2e12af54993964fde14296c7a14.png",
        "money": "100.00",
        "name": "stp",
        "image": "http://local.gfpay/assets/front/images/pst.png"
    }
}

//在线支付返回结果
{
    "status": 200,
    "data": {
        "code": 200,
        "msg": "successful",
        "data": {
            "reference": "706180301567011837",
            "barcode": "GV1825068937331875840",
            "expire": "2024-08-19 15:15:37",
            "checkout": "https://checkout-mx.eastpay.top?checkout=MjAyNDA4MTgxNTExNTcyNzUwNCZHVjE4MjUwNjg5MzczMzE4NzU4NDAmNzA2MTgwMzAxNTY3MDExODM3",
            "money": "100.00",
            "path": "http://local.gfpay/assets/tmp/37.png",
            "name": "Paycash",
            "image": "http://local.gfpay/assets/front/images/pay_cash.png"
        }
    },
    "message": "successful"
}
```

### 6) 请求返回结果参数说明:

|字段名称|   字段说明    | 类型 |     备注 |
|---------|:---------:|:-:|-------:|
| reference    |  reference   | string | | |    |
| barcode    |  barcode   | string | | |    |
| expire    |  过期时间   | string | | |    |
| money    |  支付金额   | string | | |    |
| path    |  条形码   | string | | |    |
| name    |  支付方式名称   | string | | |    |
| image    |  支付方式logo  | string | | |    |
| checkout    |  在线支付收银台地址  | string | | |    |