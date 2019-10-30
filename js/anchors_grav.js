/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function generateTableOfContents(els)
{
    console.log("generating");
    var anchoredElText, anchoredElHref, ul = document.createElement('UL');
//    document.getElementById('toc').appendChild(ul);
    for (var i = 0; i < els.length; i++) {
        anchoredElText = els[i].textContent;
        anchoredElHref = els[i].querySelector('.anchorjs-link').getAttribute('href');
        addNavItem(ul, anchoredElHref, anchoredElText, els[i].tagName);
    }
    return ul;
}

function addNavItem(ul, href, text, cls)
{
    var listItem = document.createElement('li'), anchorItem = document.createElement('a'), textNode = document.createTextNode(text);

    anchorItem.href = href;
    listItem.classList.add(cls);
    ul.appendChild(listItem);
    listItem.appendChild(anchorItem);
    anchorItem.appendChild(textNode);
}