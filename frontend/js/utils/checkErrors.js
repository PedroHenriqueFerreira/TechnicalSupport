const find = (attr_name, attr, words) => {
  let errors = '';

  if(attr) {
    words.forEach((word) => {
      if(attr.indexOf(word) === -1) {
        errors += ` ${word}`;
      }
    });

    if(errors) {
      return `O ${attr_name} precisa ter${errors}`;
    }
    return null;  
    
  } else {
    return `O campo ${attr_name} é requerido`;
  }
}

const len = (attr_name, attr, min, max) => {
  if(attr) {
    if(max !== 0) {
      if(attr.length < min || attr.length > max) {
        return `O campo ${attr_name} precisa ter entre ${min} e ${max} caracteres`;
      }
      return null;
    } else {
      if(attr.length !== min) {
        return `O campo ${attr_name} precisa ter ${min} caracteres`;
      }
      return null;
    }
  } else {
    return `O campo ${attr_name} é requerido`;
  }

}

export const name = (name, errors) => {
  const checkName = len('nome', name, 3, 30);
  if(checkName) errors.push(checkName);
}

export const email = (email, errors) => {
  const checkEmail = find('email', email, ['@', '.com']);
  if(checkEmail) errors.push(checkEmail);
}

export const password = (password, errors) => {
  const checkPass = len('senha', password, 5, 32);
  if(checkPass) errors.push(checkPass);
}

export const currentPassword = (currentPass, errors) => {
  if(currentPass) {
    const checkPass = len('senha atual', currentPass, 5, 32);
    if(checkPass) errors.push(checkPass);
  }
}

export const newPassword = (newPass, errors) => {
  const currentPass = document.querySelector('input[name="password"]');
  if(currentPass.value && !newPass) {
    errors.push('Preencha o campo senha nova para trocar sua senha');
  } else if(!currentPass.value && newPass) {
    errors.push('Preencha o campo senha atual para trocar sua senha');
  } else if(currentPass.value === newPass && newPass) {
    errors.push('Os campos senha atual e senha nova devem ser diferentes');
  } else if(currentPass.value && newPass) {
    const checkNewPass = len('senha nova', newPass, 5, 32);
    if(checkNewPass) errors.push(checkNewPass);
  }
}

export const confirmPass = (confirmPass, errors) => {
  if(confirmPass !== document.querySelector('input[name="password"]').value) {
    errors.push('Os campos de senha e confirmar senha não conferem');
  }
}

export const image = (file, errors) => {
  if(!file) {
    errors.push('A foto de perfil é requerida');
  }
}

export const images = (errors) => {
  if(!document.querySelector('.img-block div .img-view')) {
    errors.push('As fotos do equipamento é requerida');
    return true;
  }

  return false;
}

export const cpf = (cpf, errors) => {
  const checkCpf = find('CPF', cpf, ['.', '.', '-']);
  if(checkCpf) {
    errors.push(checkCpf);

    return;
  }

  const checkCpfLength = len('CPF', cpf, 14, 0);
  if(checkCpfLength) errors.push(checkCpfLength);
}

export const address = (address, errors) => {
  const checkAddress = len('endereço', address, 3, 50);
  if(checkAddress) errors.push(checkAddress);
}

export const description = (description, errors) => {
  const checkDesc = len('descrição', description, 50, 500);
  if(checkDesc) errors.push(checkDesc);
}

export const number = (errors) => {
  document.querySelectorAll('input[name^="number"]').forEach((number) => {

    const checkNumberLength = len('número', number.value, 19, 0);
    if(checkNumberLength) {
      errors.push(checkNumberLength);
      return;
    }

    const checkNumber = find('número', number.value, ['+', '(', ')', '-']);
    if(checkNumber) { 
      errors.push(checkNumber);

      return;
    }  
  });
}

export const specifications = (specifications, errors) => {
  const checkSpecifications = len('especificações', specifications, 50, 500);
  if(checkSpecifications) errors.push(checkSpecifications);
}

export const report = (report, errors) => {
  const checkSpecifications = len('relatório', report, 50, 500);
  if(checkSpecifications) errors.push(checkSpecifications);
}

export const cost = (cost, errors) => {
  if(!cost) errors.push('O campo custo é requerido');
}

export const radio = (errors) => {
  let marked = false;

  document.querySelectorAll('.radio-block input').forEach((radio) => {
      if(radio.checked) {
          marked = true;
          return;
      }
  });

  if(!marked) errors.push('Escolha um equipamento');
}